@php
    /** @var \App\Models\Order $order */
    $order = $getRecord()->loadMissing(['user', 'shippingAddress', 'items.watch.brand', 'items.watch.images', 'messages.author']);
    $client = $order->user;
    $address = $order->shippingAddress;
    $tz = config('app.timezone');
    $addressLines = $address
        ? array_filter([$address->full_name, $address->street, trim($address->postal_code.' '.$address->city), $address->state, $address->country, $address->phone])
        : [];
@endphp

<div class="elite-dossier">
    {{-- Hero band --}}
    <section class="elite-dossier__hero">
        <p class="elite-eyebrow">Acquisition #{{ $order->order_number }}</p>
        <h2 class="elite-dossier__client">{{ $client?->name ?? 'Former client' }}</h2>
        <div class="elite-dossier__meta">
            <span class="elite-italic">Submitted {{ $order->created_at->timezone($tz)->format('l j F Y · H:i') }} · {{ $order->created_at->diffForHumans() }}</span>
            @if ($order->preferred_method)
                <span class="elite-method-pill">
                    <x-filament::icon :icon="config('concierge.methods.'.$order->preferred_method.'.icon')" class="h-3.5 w-3.5" />
                    {{ $order->methodLabel() }}
                </span>
            @endif
            <span class="elite-pill is-{{ $order->status }}">{{ $order->statusLabel() }}</span>
        </div>
    </section>

    <div class="elite-dossier__grid">
        {{-- Client --}}
        <section class="elite-dossier__card">
            <p class="elite-eyebrow">Client</p>
            <div class="mt-4 flex items-center gap-4">
                <span class="elite-avatar elite-avatar--gold" aria-hidden="true">{{ \App\Filament\AvatarProviders\InitialsAvatarProvider::initials($client?->name) }}</span>
                <div class="min-w-0">
                    @if ($client)
                        <a href="{{ \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $client]) }}" target="_blank" rel="noopener" class="elite-dossier__name">
                            {{ $client->name }} <span aria-hidden="true">↗</span><span class="sr-only">(opens in a new tab)</span>
                        </a>
                        <a href="mailto:{{ $client->email }}" class="block truncate elite-muted hover:text-[color:var(--elite-gold)]" style="font-size: 13px">{{ $client->email }}</a>
                        <p class="elite-muted" style="font-size: 13px">{{ $client->phone ?: ($address?->phone ?: 'No phone on file') }}</p>
                    @else
                        <p class="elite-muted">This client account no longer exists.</p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Shipping --}}
        <section class="elite-dossier__card" x-data="{ copied: false }">
            <div class="flex items-center justify-between gap-3">
                <p class="elite-eyebrow">Ship to</p>
                @if ($addressLines)
                    <button
                        type="button"
                        class="elite-ghost-btn"
                        x-on:click="navigator.clipboard.writeText(@js(implode("\n", $addressLines))).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                    >
                        <span x-show="! copied">Copy address</span>
                        <span x-show="copied" x-cloak>Copied</span>
                    </button>
                @endif
            </div>
            @if ($addressLines)
                <address class="mt-4 not-italic" style="font-size: 14px; line-height: 1.7; color: var(--elite-mint)">
                    @foreach ($addressLines as $line)
                        <span class="block {{ $loop->first ? 'text-white' : '' }}">{{ $line }}</span>
                    @endforeach
                </address>
            @else
                <p class="mt-4 elite-muted">No shipping address on file.</p>
            @endif
        </section>
    </div>

    {{-- Pieces --}}
    <section class="mt-6">
        <p class="elite-eyebrow mb-3">Pieces requested</p>
        <div class="flex flex-col gap-3">
            @foreach ($order->items as $item)
                @php
                    $watch = $item->watch;
                    $image = \App\Support\WatchImageUrl::primary($watch);
                    $stock = $watch?->stock;
                    [$stockClass, $stockText] = match (true) {
                        $watch === null => ['rose', 'No longer in the catalogue'],
                        $stock <= 0 => ['rose', 'Out of stock — reorder required'],
                        $stock < $item->quantity => ['rose', "Only {$stock} in stock — reorder required"],
                        (int) $stock === (int) $item->quantity => ['gold', 'Last available'],
                        default => ['mint', "In stock · {$stock} remaining"],
                    };
                @endphp
                <article class="elite-dossier__item">
                    @if ($image)
                        <img src="{{ $image }}" alt="{{ $watch?->name }}" class="elite-dossier__photo" loading="lazy" width="120" height="120">
                    @else
                        <span class="elite-dossier__photo" aria-hidden="true"></span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="elite-eyebrow" style="letter-spacing: 0.25em">{{ $watch?->brand?->name }}</p>
                        <p class="elite-dossier__watch">{{ $watch?->name ?? 'A piece no longer listed' }}</p>
                        @if ($watch?->reference_number)
                            <p class="elite-muted" style="font-size: 12px; font-family: ui-monospace, monospace">Ref. {{ $watch->reference_number }}</p>
                        @endif
                        <p class="mt-2" style="font-size: 13px; color: var(--elite-mint)">
                            {{ $item->quantity }} × €{{ number_format((float) $item->unit_price, 2) }}
                        </p>
                        <p class="elite-stock-note elite-stock-note--{{ $stockClass }}">{{ $stockText }}</p>
                    </div>
                    <p class="elite-accent self-start" style="font-size: 18px; white-space: nowrap">€{{ number_format((float) $item->subtotal, 2) }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Totals --}}
    <section class="elite-dossier__card mt-6">
        <dl class="elite-totals">
            <div><dt>Subtotal</dt><dd>€{{ number_format((float) $order->subtotal, 2) }}</dd></div>
            <div><dt>Tax</dt><dd>€{{ number_format((float) $order->tax, 2) }}</dd></div>
            <div><dt>Shipping</dt><dd>{{ (float) $order->shipping > 0 ? '€'.number_format((float) $order->shipping, 2) : 'Complimentary' }}</dd></div>
            @if ($order->original_total && (float) $order->original_total !== (float) $order->total)
                <div><dt>Original total</dt><dd class="line-through elite-muted">€{{ number_format((float) $order->original_total, 2) }}</dd></div>
            @endif
            <div class="elite-totals__grand"><dt>Total</dt><dd>€{{ number_format((float) $order->total, 2) }}</dd></div>
        </dl>
    </section>

    {{-- Client note --}}
    @if ($order->customer_note)
        <section class="elite-dossier__quote mt-6">
            <span class="elite-dossier__quote-mark" aria-hidden="true">&ldquo;</span>
            <p class="elite-eyebrow">Note from the client</p>
            <p class="elite-dossier__quote-text">{{ $order->customer_note }}</p>
        </section>
    @endif

    {{-- Conversation --}}
    @if ($order->messages->isNotEmpty())
        <section class="mt-6">
            <p class="elite-eyebrow mb-3">Conversation</p>
            <ol class="flex flex-col gap-3">
                @foreach ($order->messages as $message)
                    <li @class(['elite-message', 'elite-message--atelier' => $message->isFromAtelier()])>
                        <p class="elite-message__meta">
                            {{ $message->isFromAtelier() ? 'Atelier · '.($message->author?->name ?? 'Admin') : 'Client' }}
                            · {{ $message->created_at->timezone($tz)->format('j M · H:i') }}
                        </p>
                        <p class="elite-message__body">{{ $message->body }}</p>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif
</div>
