<footer class="relative bg-primary-dark border-t border-primary-teal/30 pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-12">
            <!-- Brand -->
            <div>
                <img src="{{ asset('images/logo/elite-gold.svg') }}" alt="ÉLITE" class="h-10 w-auto mb-4">
                <p class="font-accent italic text-text-mint/70 text-sm leading-relaxed max-w-xs">
                    {{ __('A curated maison of the world\'s finest timepieces, for those who measure life in moments.') }}
                </p>

                <div class="flex items-center gap-4 mt-6">
                    <a href="#" aria-label="Instagram" class="text-text-mint/70 hover:text-accent-gold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="5" />
                            <circle cx="12" cy="12" r="4" />
                            <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Facebook" class="text-text-mint/70 hover:text-accent-gold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 9h3V6h-3a4 4 0 00-4 4v2H7v3h3v6h3v-6h3l1-3h-4v-2a1 1 0 011-1z" />
                        </svg>
                    </a>
                    <a href="#" aria-label="X" class="text-text-mint/70 hover:text-accent-gold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4l16 16M20 4L4 20" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Pinterest" class="text-text-mint/70 hover:text-accent-gold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 17c1-4 1.5-6 1.5-8a2 2 0 114 0c0 1.5-1 4-1 5.5a1.5 1.5 0 003 0c0-3.5-2-6.5-5.5-6.5A6 6 0 005.5 13c0 1.8 1 2.7 1.5 2.7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Shop -->
            <div>
                <h4 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-5">{{ __('Shop') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('shop.index') }}" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('All Watches') }}</a></li>
                    <li><a href="{{ route('shop.index', ['sort' => 'newest']) }}" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('New Arrivals') }}</a></li>
                    <li><a href="{{ route('shop.index', ['in_stock' => 1]) }}" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('In Stock') }}</a></li>
                    <li><a href="{{ route('brands.index') }}" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Brands') }}</a></li>
                    <li><a href="{{ route('wishlist.index') }}" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Wishlist') }}</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-5">{{ __('Support') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('contact.index') }}" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Contact Us') }}</a></li>
                    <li><a href="#" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Shipping & Delivery') }}</a></li>
                    <li><a href="#" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Returns & Warranty') }}</a></li>
                    <li><a href="#" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Authentication') }}</a></li>
                    <li><a href="#" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('FAQ') }}</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h4 class="font-heading text-white text-sm uppercase tracking-[0.15em] mb-5">{{ __('Legal') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('about') }}" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('About ÉLITE') }}</a></li>
                    <li><a href="#" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Terms of Service') }}</a></li>
                    <li><a href="#" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="#" class="text-text-mint/70 hover:text-accent-gold transition">{{ __('Cookie Policy') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="hairline-gold"></div>

        <div class="py-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <p class="text-xs text-text-mint/50 order-2 sm:order-1">
                &copy; {{ now()->year }} {{ __('ÉLITE Maison Horlogère. All rights reserved.') }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 order-1 sm:order-2 text-text-mint/50">
                {{-- Settlement is arranged privately after each request is reviewed. --}}
                @foreach (['WIRE TRANSFER', 'IN BOUTIQUE', 'FINANCING', 'DIGITAL ASSETS'] as $method)
                    <span class="px-2 py-1 border border-text-mint/20 text-[10px] tracking-widest whitespace-nowrap">{{ $method }}</span>
                @endforeach
            </div>
        </div>
    </div>
</footer>
