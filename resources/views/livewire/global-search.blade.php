@php
    use App\Support\SearchHighlighter;
    use App\Support\WatchImageUrl;

    $q = trim($query);
    $searchable = $this->hasSearchableQuery();
    $results = $this->results;
    $total = $this->totalResults;
    $brands = $this->brandMatches;
    $categories = $this->categoryMatches;
@endphp

<div
    x-data="eliteSearch({ searchUrl: @js(route('search')), minLength: {{ \App\Livewire\GlobalSearch::MIN_LENGTH }} })"
    x-on:open-search.window="openSearch($event)"
    x-on:keydown.window="globalShortcut($event)"
>
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[90]"
        role="dialog"
        aria-modal="true"
        aria-label="Search the maison"
        x-on:keydown="trapAndNavigate($event)"
    >
        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="transition-opacity duration-200 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-200 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-surface-ink/70"
            x-on:click="close()"
            aria-hidden="true"
        ></div>

        {{-- Drawer --}}
        <div
            x-show="open"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-6"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-6"
            x-ref="panel"
            x-on:click.self="close()"
            class="search-drawer absolute inset-0 overflow-y-auto overscroll-contain"
        >
            <div class="mx-auto max-w-[980px] px-6 pb-24 pt-6 sm:pt-[80px]" x-on:click.self="close()">
                {{-- Top row --}}
                <div class="flex items-center justify-between">
                    <img src="{{ asset('images/logo/elite-nav.svg') }}" alt="ÉLITE" class="h-6 w-auto opacity-60">
                    <button type="button" x-on:click="close()" x-ref="closeButton" class="group grid h-11 w-11 place-items-center" aria-label="Close search">
                        <span class="grid h-7 w-7 place-items-center border border-accent-gold text-accent-gold transition-transform duration-300 group-hover:rotate-90 group-focus-visible:rotate-90">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path stroke-linecap="round" d="M5 5l14 14M19 5 5 19"/></svg>
                        </span>
                    </button>
                </div>

                {{-- Input --}}
                <div class="search-input-row mt-10 flex items-center gap-4 border-b border-accent-gold/30 pb-3 transition-colors focus-within:border-accent-gold sm:mt-14">
                    <svg class="h-6 w-6 shrink-0 text-accent-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m0 0A7.5 7.5 0 1 0 5.2 5.2a7.5 7.5 0 0 0 10.6 10.6Z"/></svg>
                    <label for="global-search-input" class="sr-only">Search the maison</label>
                    <input
                        id="global-search-input"
                        x-ref="input"
                        type="search"
                        wire:model.live.debounce.250ms="query"
                        x-on:input="active = -1"
                        placeholder="Search the maison..."
                        autocomplete="off"
                        spellcheck="false"
                        maxlength="80"
                        role="combobox"
                        aria-expanded="{{ $searchable && $results->isNotEmpty() ? 'true' : 'false' }}"
                        aria-controls="global-search-results"
                        aria-autocomplete="list"
                        x-bind:aria-activedescendant="active >= 0 ? 'search-result-' + active : null"
                        class="search-input min-w-0 flex-1 border-0 bg-transparent p-0 font-heading text-white placeholder:font-accent placeholder:italic placeholder:text-text-mint/30 focus:outline-none focus:ring-0"
                    >
                    <span class="hidden shrink-0 font-accent italic text-sm text-text-mint/50 md:inline">Press ↵ to search · ESC to close</span>
                </div>

                {{-- Results --}}
                <div id="global-search-results" class="mt-10" aria-live="polite">
                    {{-- Screen-reader summary of what changed --}}
                    <p class="sr-only">
                        @if ($searchable)
                            {{ $total === 0 ? "No pieces match {$q}." : trans_choice('{1} One piece found.|[2,*] :count pieces found.', $total) }}
                        @endif
                    </p>

                    {{-- C · searching --}}
                    <div wire:loading.flex wire:target="query" class="flex-col items-center gap-4 py-10" aria-hidden="true">
                        <span class="search-pulse block h-px w-full max-w-[420px]"></span>
                        <span class="font-accent italic text-base text-text-mint/60">Searching the vault...</span>
                    </div>

                    <div wire:loading.remove wire:target="query">
                        @if ($q === '')
                            {{-- A · nothing typed yet --}}
                            <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
                                <section aria-labelledby="recent-searches-title">
                                    <h2 id="recent-searches-title" class="font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">Recent searches</h2>
                                    <template x-if="recent.length">
                                        <div>
                                            <ul class="mt-5 space-y-1">
                                                <template x-for="term in recent" :key="term">
                                                    <li>
                                                        <button type="button" x-on:click="useTerm(term)" class="group flex min-h-[44px] w-full items-center gap-4 text-left text-[15px] text-white transition hover:text-accent-gold" data-search-item>
                                                            <span class="h-px w-4 bg-accent-gold/70 transition-all group-hover:w-6" aria-hidden="true"></span>
                                                            <span x-text="term"></span>
                                                        </button>
                                                    </li>
                                                </template>
                                            </ul>
                                            <button type="button" x-on:click="clearHistory()" class="mt-3 min-h-[44px] font-accent italic text-sm text-text-mint/50 transition hover:text-accent-gold">Clear history</button>
                                        </div>
                                    </template>
                                    <p x-show="! recent.length" class="mt-5 font-accent italic text-base text-text-mint/50">Your recent searches will appear here.</p>
                                </section>

                                <section aria-labelledby="search-suggestions-title">
                                    <h2 id="search-suggestions-title" class="font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">Suggestions</h2>
                                    <div class="mt-5 flex flex-wrap gap-3">
                                        @foreach ($suggestionPills as $pill)
                                            @if (isset($pill['query']))
                                                <button type="button" x-on:click="useTerm(@js($pill['query']))" class="search-pill" data-search-item>{{ $pill['label'] }}</button>
                                            @else
                                                <a href="{{ $pill['url'] }}" x-on:click="remember(@js($pill['label']))" class="search-pill" data-search-item>{{ $pill['label'] }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                </section>
                            </div>
                        @elseif (! $searchable)
                            {{-- B · too short --}}
                            <p class="py-10 text-center font-accent italic text-lg text-text-mint/50">Keep typing to search the maison...</p>
                        @elseif ($results->isEmpty())
                            {{-- E · nothing found --}}
                            <div class="flex flex-col items-center px-2 py-[60px] text-center">
                                <div class="maison-divider" aria-hidden="true"><span></span></div>
                                <p class="mt-8 font-accent italic text-[13px] uppercase tracking-[0.3em] text-accent-gold">Not in the vault</p>
                                <h2 class="mt-3 font-heading text-2xl text-white">No pieces match ‘{{ $q }}’.</h2>
                                <p class="mt-3 text-sm text-text-mint/70">Our atelier may be able to source what you're looking for.</p>
                                <div class="mt-8 flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                                    <a href="{{ route('contact.index', ['subject' => 'unlisted', 'prefill' => $q]) }}#written-request" x-on:click="remember(@js($q))" class="search-ghost-btn" data-search-item>Request this piece</a>
                                    <a href="{{ route('shop.index') }}" class="search-ghost-btn" data-search-item>Browse the catalog</a>
                                </div>
                            </div>
                        @else
                            {{-- D · results --}}
                            <ul class="grid grid-cols-1 gap-4 md:grid-cols-2" role="listbox" aria-label="Matching pieces">
                                @foreach ($results as $watch)
                                    @php
                                        $image = WatchImageUrl::primary($watch);
                                    @endphp
                                    <li role="option" id="search-result-{{ $loop->index }}" x-bind:aria-selected="active === {{ $loop->index }}" wire:key="search-{{ $watch->id }}-{{ md5($q) }}">
                                        <a
                                            href="{{ route('shop.show', $watch->slug) }}"
                                            x-on:click="remember(@js($q))"
                                            x-bind:class="active === {{ $loop->index }} && 'is-active'"
                                            class="search-result flex gap-5"
                                            style="animation-delay: {{ $loop->index * 50 }}ms"
                                            data-search-item
                                            data-search-result
                                        >
                                            @if ($image)
                                                <img src="{{ $image }}" alt="" class="h-24 w-24 shrink-0 border border-accent-gold/50 bg-primary-dark object-cover" loading="lazy" decoding="async" width="96" height="96">
                                            @else
                                                <span class="h-24 w-24 shrink-0 border border-accent-gold/50 bg-primary-dark" aria-hidden="true"></span>
                                            @endif
                                            <span class="flex min-w-0 flex-1 flex-col">
                                                <span class="font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">{{ SearchHighlighter::highlight($watch->brand?->name, $q, 'text-white') }}</span>
                                                <span class="mt-1 font-heading text-[17px] leading-snug text-white">{{ SearchHighlighter::highlight($watch->name, $q, 'text-accent-gold') }}</span>
                                                @if ($watch->reference_number)
                                                    <span class="mt-1 font-mono text-xs text-text-mint/60">Ref. {{ SearchHighlighter::highlight($watch->reference_number, $q, 'text-accent-gold') }}</span>
                                                @endif
                                                <span class="mt-auto pt-2 font-heading text-lg text-accent-gold">€{{ number_format((float) ($watch->discount_price ?? $watch->price), 2, ',', '.') }}</span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            @if ($total > $results->count())
                                <div class="mt-8 text-center">
                                    <a href="{{ route('search', ['q' => $q]) }}" x-on:click="remember(@js($q))" class="inline-flex min-h-[44px] items-center gap-2 text-xs uppercase tracking-[0.25em] text-accent-gold transition hover:text-accent-peach" data-search-item>
                                        View all {{ $total }} results <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            @endif

                            @if ($categories->isNotEmpty() || $brands->isNotEmpty())
                                <div class="mt-12 grid grid-cols-1 gap-8 border-t border-accent-gold/15 pt-8 sm:grid-cols-2">
                                    @if ($categories->isNotEmpty())
                                        <div>
                                            <p class="font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">By category</p>
                                            <div class="mt-4 flex flex-wrap gap-3">
                                                @foreach ($categories as $category)
                                                    <a href="{{ route('shop.index', ['category' => [$category->id]]) }}" class="search-pill" data-search-item>{{ $category->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if ($brands->isNotEmpty())
                                        <div>
                                            <p class="font-accent italic text-[11px] uppercase tracking-[0.3em] text-accent-gold">By brand</p>
                                            <div class="mt-4 flex flex-wrap gap-3">
                                                @foreach ($brands as $brand)
                                                    <a href="{{ route('shop.index', ['brand' => [$brand->id]]) }}" class="search-pill" data-search-item>{{ $brand->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
