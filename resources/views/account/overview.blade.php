<x-account-layout active="overview" :title="__('Welcome back, :name', ['name' => explode(' ', auth()->user()->name)[0]])">

    <!-- Stat tiles -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-14">
        <div class="border border-primary-teal/20 p-6 text-center">
            <p class="font-heading text-4xl text-accent-gold mb-2">{{ $ordersCount }}</p>
            <p class="text-xs uppercase tracking-[0.2em] text-text-mint/60">{{ __('Acquisitions') }}</p>
        </div>
        <div class="border border-primary-teal/20 p-6 text-center">
            <p class="font-heading text-4xl text-accent-gold mb-2">{{ $wishlistCount }}</p>
            <p class="text-xs uppercase tracking-[0.2em] text-text-mint/60">{{ __('Wishlist Items') }}</p>
        </div>
        <div class="border border-primary-teal/20 p-6 text-center">
            <p class="font-heading text-4xl text-accent-gold mb-2">{{ $addresses->count() }}</p>
            <p class="text-xs uppercase tracking-[0.2em] text-text-mint/60">{{ __('Saved Addresses') }}</p>
        </div>
    </div>

    <!-- Recent orders -->
    <div class="mb-14">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-heading text-white text-2xl">{{ __('Recent Acquisitions') }}</h2>
            <a href="{{ route('account.orders') }}" class="text-xs uppercase tracking-[0.2em] text-accent-gold hover:text-accent-peach transition">{{ __('View all') }}</a>
        </div>

        @forelse ($recentOrders as $order)
            <a href="{{ route('account.orders.show', $order->order_number) }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 py-4 border-b border-primary-teal/10 hover:bg-primary-teal/5 px-3 -mx-3 transition">
                <div>
                    <p class="text-white font-mono text-sm">{{ $order->order_number }}</p>
                    <p class="text-xs text-text-mint/50 mt-1">{{ $order->created_at->format('F j, Y') }} &middot; {{ $order->items->count() }} {{ __('item(s)') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <x-status-pill :status="$order->status" />
                    <span class="font-heading text-accent-gold">€{{ number_format((float) $order->total, 2, ',', '.') }}</span>
                </div>
            </a>
        @empty
            <p class="text-text-mint/60 py-6">{{ __('You haven\'t placed any orders yet.') }}</p>
        @endforelse
    </div>

    <!-- Saved addresses -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-heading text-white text-2xl">{{ __('Saved Addresses') }}</h2>
            <a href="{{ route('account.addresses') }}" class="text-xs uppercase tracking-[0.2em] text-accent-gold hover:text-accent-peach transition">{{ __('Manage') }}</a>
        </div>

        @forelse ($addresses->take(2) as $address)
            <div class="border border-primary-teal/20 p-5 mb-3">
                <div class="flex items-center gap-2 mb-1">
                    <p class="text-white text-sm">{{ $address->full_name }}</p>
                    @if ($address->is_default)
                        <span class="text-[10px] uppercase tracking-widest text-accent-gold border border-accent-gold/40 px-2 py-0.5">{{ __('Default') }}</span>
                    @endif
                </div>
                <p class="text-sm text-text-mint/60">{{ $address->street }}, {{ $address->city }}, {{ $address->country }}</p>
            </div>
        @empty
            <p class="text-text-mint/60 py-2">{{ __('No saved addresses yet.') }}</p>
        @endforelse
    </div>

</x-account-layout>
