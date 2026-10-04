<div class="elite-login">
    {{-- ─── Left: cinematic hero (≥ 1024px only) ─── --}}
    <aside class="elite-hero" aria-hidden="true">
        <div class="elite-hero__image"></div>
        <div class="elite-hero__overlay"></div>

        {{-- Slow-drifting gold particles + brighter "dust in the light" specks --}}
        <div class="elite-hero__particles">
            @foreach (range(1, 8) as $i)
                <span class="elite-particle elite-particle--{{ $i }}"></span>
            @endforeach
            @foreach (range(1, 4) as $i)
                <span class="elite-speck elite-speck--{{ $i }}"></span>
            @endforeach
        </div>

        <div class="elite-hero__glass"></div>

        <div class="elite-hero__content">
            <span class="elite-hero__rule"></span>
            <p class="elite-hero__eyebrow">Maison Élite</p>
            <h2 class="elite-hero__headline">The Vault Awaits.</h2>
            <p class="elite-hero__subhead">Private access for the maison's keepers.</p>

            <span class="elite-hero__vline"></span>
            <ul class="elite-hero__meta">
                <li>Est. MMXXVI</li>
                <li>Prishtina · Switzerland</li>
                <li>By invitation only</li>
            </ul>
        </div>
    </aside>

    {{-- ─── Right: sign-in ─── --}}
    <section class="elite-panel">
        <div class="elite-panel__glow" aria-hidden="true"></div>

        <main class="elite-card">
            <img
                src="{{ asset('images/logo/elite-gold.svg') }}"
                alt="ÉLITE — Maison Horlogère"
                class="elite-card__logo"
            />

            <div class="elite-divider" aria-hidden="true">
                <span class="elite-divider__line"></span>
                <span class="elite-divider__diamond"></span>
                <span class="elite-divider__line elite-divider__line--rev"></span>
            </div>

            <p class="elite-card__eyebrow">Administration</p>
            <h1 class="elite-card__heading">{{ $this->getHeading() }}</h1>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

            <div class="elite-card__form">
                <x-filament-panels::form id="form" wire:submit="authenticate">
                    {{ $this->form }}

                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </x-filament-panels::form>
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

            <nav class="elite-card__links" aria-label="Account help">
                @if (filament()->hasPasswordReset())
                    <a href="{{ filament()->getRequestPasswordResetUrl() }}">Forgot password?</a>
                    <span class="elite-card__dot" aria-hidden="true">·</span>
                @endif
                <a href="{{ route('contact.index') }}">Request access</a>
            </nav>

            <p class="elite-card__fineprint">
                Protected by two-factor authentication · All sessions logged
            </p>
        </main>

        <footer class="elite-panel__footer">
            ÉLITE · MAISON HORLOGÈRE · © {{ now()->year }}
        </footer>
    </section>
</div>
