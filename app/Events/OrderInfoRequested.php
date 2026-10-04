<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderInfoRequested
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order, public OrderMessage $message) {}
}
