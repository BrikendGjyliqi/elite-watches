<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\AcquisitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function overview(Request $request)
    {
        $user = $request->user();

        $recentOrders = $user->orders()->with('items')->latest()->take(5)->get();
        $wishlistCount = $user->wishlists()->count();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        return view('account.overview', [
            'recentOrders' => $recentOrders,
            'wishlistCount' => $wishlistCount,
            'addresses' => $addresses,
            'ordersCount' => $user->orders()->count(),
        ]);
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with('items.watch.images')->latest()->paginate(10);

        return view('account.orders', [
            'orders' => $orders,
        ]);
    }

    public function orderShow(Request $request, string $orderNumber)
    {
        $order = Order::with(['items.watch.images', 'items.watch.brand', 'shippingAddress', 'messages.author'])
            ->where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('account.order-show', [
            'order' => $order,
        ]);
    }

    /** The client answers a question from the atelier. */
    public function orderReply(Request $request, string $orderNumber, AcquisitionService $acquisitions): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        abort_unless($order->isOpen(), 403, 'This request is no longer open for messages.');

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [
            'body.required' => 'Please write your message to the atelier.',
        ]);

        $acquisitions->clientReply($order, $request->user(), $validated['body']);

        return redirect()
            ->to(route('account.orders.show', $order->order_number).'#conversation')
            ->with('status', 'message-sent');
    }

    public function addresses(Request $request)
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();

        return view('account.addresses', [
            'addresses' => $addresses,
        ]);
    }

    public function addressStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->addresses()->create([
            ...$validated,
            'is_default' => ! $request->user()->addresses()->exists(),
        ]);

        return back()->with('status', 'address-added');
    }

    public function addressSetDefault(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('status', 'address-updated');
    }

    public function addressDestroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $address->delete();

        return back()->with('status', 'address-deleted');
    }
}
