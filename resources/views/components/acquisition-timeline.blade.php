@props(['order'])

@php
    // Where the request sits in its journey: 1 submitted · 2 review · 3 confirmation & payment · 4 delivery · 5 complete
    $position = match ($order->status) {
        'requested', 'under_review' => 2,
        'approved', 'awaiting_payment' => 3,
        'paid' => 4,
        'shipped' => 4,
        'delivered' => 5,
        default => null, // declined / cancelled
    };

    $stopped = $position === null;
    $stoppedAt = $order->status === 'declined' ? 2 : 3;

    $steps = [
        1 => ['Request submitted', $order->created_at],
        2 => ['Review by atelier', $order->approved_at ?? $order->declined_at],
        3 => ['Confirmation & payment', null],
        4 => ['Delivery', null],
    ];
@endphp

<ol {{ $attributes->class(['relative']) }}>
    @foreach ($steps as $index => [$label, $date])
        @php
            if ($stopped) {
                $state = $index < $stoppedAt ? 'done' : ($index === $stoppedAt ? 'stopped' : 'muted');
            } else {
                $state = $index < $position ? 'done' : ($index === $position ? 'active' : 'upcoming');
            }
            if ($state === 'stopped') {
                $label = $order->status === 'declined' ? 'Request declined' : 'Request cancelled';
                $date = $order->declined_at ?? $order->updated_at;
            }
        @endphp
        <li class="relative flex gap-5 pb-8 last:pb-0">
            @unless ($loop->last)
                <span class="absolute left-[11px] top-7 bottom-1 w-px {{ $state === 'done' ? 'bg-accent-gold/70' : 'bg-text-mint/15' }}" aria-hidden="true"></span>
            @endunless

            <span @class([
                'relative z-10 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border',
                'border-accent-gold bg-accent-gold text-primary-dark' => $state === 'done',
                'border-accent-gold bg-primary-dark acq-pulse-ring' => $state === 'active',
                'border-[#E8A598] bg-primary-dark text-[#E8A598]' => $state === 'stopped',
                'border-text-mint/25 bg-primary-dark' => in_array($state, ['upcoming', 'muted'], true),
            ])>
                @if ($state === 'done')
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                @elseif ($state === 'active')
                    <span class="h-2 w-2 rounded-full bg-accent-gold"></span>
                @elseif ($state === 'stopped')
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                @endif
            </span>

            <div class="min-w-0">
                <p @class([
                    'text-sm',
                    'text-white' => in_array($state, ['done', 'active'], true),
                    'text-[#E8A598]' => $state === 'stopped',
                    'text-text-mint/45' => in_array($state, ['upcoming', 'muted'], true),
                ])>
                    {{ $label }}
                    @if ($state === 'active')
                        <span class="sr-only">({{ __('current step') }})</span>
                    @endif
                </p>
                @if ($date && in_array($state, ['done', 'stopped'], true))
                    <p class="font-accent italic text-sm text-text-mint/50">{{ $date->timezone(config('app.timezone'))->format('j F Y · H:i') }}</p>
                @elseif ($state === 'active')
                    <p class="font-accent italic text-sm text-accent-gold/80">{{ __('In progress') }}</p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
