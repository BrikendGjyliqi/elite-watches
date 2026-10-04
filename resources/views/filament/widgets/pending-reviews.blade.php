<x-filament-widgets::widget>
    <article class="elite-tile">
        <header class="elite-tile__head">
            <p class="elite-eyebrow">Reviews · Awaiting approval</p>
            <a href="{{ \App\Filament\Resources\ReviewResource::getUrl('index') }}" class="elite-tile__link">All →</a>
        </header>

        @if ($reviews->isEmpty())
            <x-elite.empty icon="quill">Every voice has been heard · No reviews await approval.</x-elite.empty>
        @else
            <ul class="elite-list">
                @foreach ($reviews as $review)
                    <li class="py-4 first:pt-0" wire:key="review-{{ $review->id }}">
                        <div class="flex items-center justify-between gap-3">
                            <span class="elite-stars" aria-label="{{ $review->rating }} out of 5 stars">
                                @for ($i = 1; $i <= 5; $i++)<span @class(['is-off' => $i > $review->rating])>★</span>@endfor
                            </span>
                            <span class="elite-italic elite-muted" style="font-size: 13px">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-2" style="font-size: 13px; line-height: 1.6; color: var(--elite-mint)">
                            @if ($review->title)<span style="color: #fff">{{ $review->title }} — </span>@endif{{ str($review->body)->limit(110) }}
                        </p>
                        <p class="mt-1 elite-italic elite-muted" style="font-size: 13px">{{ $review->user?->name }} · on {{ $review->watch?->name }}</p>
                        <div class="mt-3 flex gap-2">
                            <button type="button" class="elite-ghost-btn" wire:click="approve({{ $review->id }})" wire:loading.attr="disabled">Approve</button>
                            <button type="button" class="elite-ghost-btn elite-ghost-btn--rose" wire:click="reject({{ $review->id }})" wire:confirm="Reject and remove this review?" wire:loading.attr="disabled">Reject</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </article>
</x-filament-widgets::widget>
