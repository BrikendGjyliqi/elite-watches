/**
 * Client side of the storefront search drawer (resources/views/livewire/global-search.blade.php):
 * open/close, focus trap, keyboard navigation and per-device recent searches.
 */
const STORAGE_KEY = 'elite.recentSearches';
const MAX_RECENT = 5;

const readRecent = () => {
    try {
        const stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        return Array.isArray(stored) ? stored.filter((t) => typeof t === 'string').slice(0, MAX_RECENT) : [];
    } catch {
        return [];
    }
};

const writeRecent = (terms) => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(terms));
    } catch {
        // Private mode / storage disabled: recent searches simply aren't kept.
    }
};

const isTypingTarget = (el) => el && (el.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName));

export default ({ searchUrl, minLength }) => ({
    open: false,
    active: -1,
    recent: [],
    returnFocusTo: null,

    init() {
        this.recent = readRecent();
    },

    openSearch(event) {
        if (this.open) return;
        this.returnFocusTo = event?.detail?.trigger ?? document.activeElement;
        this.recent = readRecent();
        this.active = -1;
        this.open = true;
        document.documentElement.style.overflow = 'hidden';
        this.$nextTick(() => setTimeout(() => this.$refs.input?.focus(), 30));
    },

    close() {
        if (!this.open) return;
        this.open = false;
        document.documentElement.style.overflow = '';
        this.returnFocusTo?.focus?.();
    },

    /** "/" opens search from anywhere except while typing; ESC closes. */
    globalShortcut(event) {
        if (event.key === 'Escape' && this.open) {
            event.preventDefault();
            this.close();
            return;
        }

        if (event.key === '/' && !this.open && !event.ctrlKey && !event.metaKey && !event.altKey && !isTypingTarget(event.target)) {
            event.preventDefault();
            this.openSearch();
        }
    },

    results() {
        return [...this.$refs.panel.querySelectorAll('[data-search-result]')];
    },

    /** Tab order: input → close → everything else in reading order. */
    focusables() {
        const { input, closeButton } = this.$refs;
        const nodes = this.$refs.panel.querySelectorAll('input, button, a[href], [tabindex]:not([tabindex="-1"])');
        const rest = [...nodes].filter((el) => !el.disabled && el.offsetParent !== null && el !== input && el !== closeButton);

        return [input, closeButton, ...rest];
    },

    trapAndNavigate(event) {
        const results = this.results();

        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            if (!results.length) return;
            event.preventDefault();
            const step = event.key === 'ArrowDown' ? 1 : -1;
            this.active = (this.active + step + results.length) % results.length;
            results[this.active].scrollIntoView({ block: 'nearest' });
            return;
        }

        if (event.key === 'Enter' && event.target === this.$refs.input) {
            event.preventDefault();
            if (this.active >= 0 && results[this.active]) {
                results[this.active].click();
                return;
            }
            const term = this.$refs.input.value.trim();
            if (term.length >= minLength) {
                this.remember(term);
                window.location.href = `${searchUrl}?q=${encodeURIComponent(term)}`;
            }
            return;
        }

        if (event.key === 'Tab') {
            // Keep focus inside the drawer: input → close → results/links → back to input.
            const items = this.focusables();
            if (!items.length) return;
            const index = items.indexOf(document.activeElement);
            const next = event.shiftKey
                ? items[index <= 0 ? items.length - 1 : index - 1]
                : items[index === -1 || index === items.length - 1 ? 0 : index + 1];
            event.preventDefault();
            next.focus();
        }
    },

    remember(term) {
        const clean = String(term || '').trim().slice(0, 80);
        if (clean.length < minLength) return;
        this.recent = [clean, ...this.recent.filter((t) => t.toLowerCase() !== clean.toLowerCase())].slice(0, MAX_RECENT);
        writeRecent(this.recent);
    },

    useTerm(term) {
        this.active = -1;
        this.$wire.set('query', term);
        this.$nextTick(() => this.$refs.input?.focus());
    },

    clearHistory() {
        this.recent = [];
        writeRecent([]);
        this.$refs.input?.focus();
    },
});
