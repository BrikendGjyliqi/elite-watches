<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitAcquisitionRequest;
use App\Models\Address;
use App\Models\Order;
use App\Services\AcquisitionService;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * "Private Acquisition": the client submits a request that the atelier reviews.
 * No payment is taken online — settlement is arranged after approval.
 */
class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected AcquisitionService $acquisitions,
    ) {}

    public function index(Request $request)
    {
        if ($this->cart->items()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('pages.checkout', [
            'items' => $this->cart->items()->load('watch.brand'),
            'totals' => $this->cart->calculateTotals($this->cart->subtotal()),
            'addresses' => $request->user()->addresses()->get(),
            'methods' => config('concierge.methods'),
        ]);
    }

    public function submit(SubmitAcquisitionRequest $request): RedirectResponse
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = $request->validated();
        $client = $request->user();

        $address = filled($data['address_id'] ?? null)
            ? Address::where('user_id', $client->id)->findOrFail($data['address_id'])
            : $client->addresses()->create([
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'street' => $data['street'],
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'],
                'country' => $data['country'],
                'is_default' => ! $client->addresses()->exists(),
            ]);

        $order = $this->acquisitions->submit(
            client: $client,
            address: $address,
            items: $items,
            totals: $this->cart->calculateTotals($this->cart->subtotal()),
            method: $data['preferred_method'],
            note: $data['customer_note'] ?? null,
        );

        $this->cart->clear();

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(Request $request, string $orderNumber)
    {
        $order = Order::with(['items.watch.brand', 'shippingAddress'])
            ->where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('pages.checkout.confirmation', [
            'order' => $order,
        ]);
    }
}
