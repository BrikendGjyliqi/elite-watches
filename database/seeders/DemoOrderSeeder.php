<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Watch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Optional demo data for the admin dashboard: ~70 orders spread over the last
 * year. Order numbers are prefixed "DEMO-" so they can be removed with:
 *
 *   add:    php artisan db:seed --class=DemoOrderSeeder
 *   remove: Order::where('order_number', 'like', 'DEMO-%')->delete();
 *
 * Not called from DatabaseSeeder; stock levels are left untouched.
 */
class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->pluck('id');
        $watches = Watch::all(['id', 'price', 'discount_price']);

        if ($customers->isEmpty() || $watches->isEmpty()) {
            return;
        }

        mt_srand(2026);

        activity()->withoutLogs(function () use ($customers, $watches): void {
            DB::transaction(function () use ($customers, $watches): void {
                foreach (range(1, 72) as $n) {
                    // Weighted towards recent weeks so the 30-day view has texture.
                    $daysAgo = $n <= 40 ? mt_rand(0, 34) : mt_rand(35, 360);
                    $createdAt = now()->subDays($daysAgo)->setTime(mt_rand(9, 21), mt_rand(0, 59));

                    $status = $daysAgo < 3
                        ? ['pending', 'paid'][mt_rand(0, 1)]
                        : ['paid', 'shipped', 'delivered', 'delivered', 'delivered', 'cancelled', 'pending'][mt_rand(0, 6)];

                    $lines = $watches->random(mt_rand(1, 2));
                    $subtotal = 0;
                    $items = [];

                    foreach ($lines as $watch) {
                        $unit = (float) ($watch->discount_price ?? $watch->price);
                        $quantity = mt_rand(1, 10) === 1 ? 2 : 1;
                        $subtotal += $unit * $quantity;
                        $items[] = [
                            'watch_id' => $watch->id,
                            'quantity' => $quantity,
                            'unit_price' => $unit,
                            'subtotal' => $unit * $quantity,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ];
                    }

                    $order = new Order();
                    $order->forceFill([
                        'user_id' => $customers->random(),
                        'order_number' => sprintf('DEMO-%s-%03d', $createdAt->format('ymd'), $n),
                        'status' => $status,
                        'subtotal' => $subtotal,
                        'tax' => 0,
                        'shipping' => 0,
                        'total' => $subtotal,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ])->save();

                    $order->items()->insert(array_map(fn (array $item): array => $item + ['order_id' => $order->id], $items));
                }
            });
        });
    }
}
