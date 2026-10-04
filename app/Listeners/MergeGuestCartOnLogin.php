<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class MergeGuestCartOnLogin
{
    public function __construct(protected CartService $cart) {}

    public function handle(Login $event): void
    {
        $this->cart->mergeSessionIntoUser(Session::getId(), $event->user);
    }
}
