<x-filament-widgets::widget>
    <article class="elite-tile">
        <header class="elite-tile__head">
            <p class="elite-eyebrow">Recent customers</p>
            <a href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" class="elite-tile__link">All →</a>
        </header>

        @if ($customers->isEmpty())
            <x-elite.empty>No keepers yet · The maison awaits its first guest.</x-elite.empty>
        @else
            <div class="elite-list flex flex-col">
                @foreach ($customers as $customer)
                    <a href="{{ $urlFor($customer) }}" class="elite-row-link flex items-center gap-3 py-3">
                        <span class="elite-avatar" aria-hidden="true">{{ \App\Filament\AvatarProviders\InitialsAvatarProvider::initials($customer->name) }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate" style="font-size: 14px; color: #fff">{{ $customer->name }}</span>
                            <span class="block truncate elite-muted" style="font-size: 12px">{{ $customer->email }}</span>
                        </span>
                        <span class="elite-italic" style="font-size: 13px; color: rgba(209, 232, 226, 0.6); white-space: nowrap">{{ $customer->created_at->format('j M Y') }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </article>
</x-filament-widgets::widget>
