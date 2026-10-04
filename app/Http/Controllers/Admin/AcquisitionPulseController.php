<?php

namespace App\Http\Controllers\Admin;

use App\Filament\Resources\AcquisitionResource;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tiny JSON feed polled by the admin panel to raise desktop notifications
 * for acquisition requests submitted after `since` (an order id).
 */
class AcquisitionPulseController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $since = (int) $request->query('since', 0);

        $latestId = (int) Order::max('id');

        // First poll only establishes the baseline, so a fresh login doesn't replay old requests.
        $fresh = $since > 0
            ? Order::with('user:id,name')
                ->where('id', '>', $since)
                ->where('status', 'requested')
                ->oldest('id')
                ->limit(5)
                ->get()
                ->map(fn (Order $order): array => [
                    'id' => $order->id,
                    'title' => "New acquisition request · {$order->order_number}",
                    'body' => $order->user?->name.' · €'.number_format((float) $order->total, 2).' · '.($order->methodLabel() ?? 'No preference'),
                    'url' => AcquisitionResource::reviewUrl($order),
                ])
            : collect();

        return response()->json([
            'latest' => $latestId,
            'pending' => Order::where('status', 'requested')->count(),
            'requests' => $fresh,
        ]);
    }
}
