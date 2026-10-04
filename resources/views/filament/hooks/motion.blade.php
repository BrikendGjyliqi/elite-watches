{{-- Fade cards in as they scroll into view. Elements are only hidden once JS has
     claimed them, so nothing disappears if this script never runs. --}}
<script>
    (() => {
        if (window.__eliteReveal) return;
        window.__eliteReveal = true;

        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const selector = '.elite-dash-hero, .elite-tile, .fi-section, .fi-ta-ctn, .fi-resource-list-records-page .fi-header';

        const observer = reduced || !('IntersectionObserver' in window)
            ? null
            : new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, { rootMargin: '0px 0px -40px 0px', threshold: 0.05 });

        const claim = (root = document) => {
            if (!observer) return;
            root.querySelectorAll(selector).forEach((el) => {
                if (el.dataset.eliteReveal) return;
                el.dataset.eliteReveal = '1';
                if (el.closest('.fi-modal, .fi-dropdown-panel, .fi-sidebar')) return;
                el.classList.add('elite-reveal');
                observer.observe(el);
            });
        };

        // Filament persists the sidebar as open by default, which covers the page on
        // tablets/phones at first visit; start closed below the lg breakpoint.
        document.addEventListener('alpine:initialized', () => {
            if (window.innerWidth < 1024) window.Alpine.store('sidebar')?.close();
        });

        document.addEventListener('DOMContentLoaded', () => claim());
        document.addEventListener('livewire:navigated', () => claim());
    })();
</script>
