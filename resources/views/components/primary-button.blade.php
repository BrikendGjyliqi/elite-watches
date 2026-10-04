<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-teal border border-transparent font-sans text-xs text-white uppercase tracking-[0.2em] hover:bg-primary-teal/80 hover:shadow-lg hover:shadow-primary-teal/30 focus:outline-none focus:ring-2 focus:ring-accent-gold focus:ring-offset-2 focus:ring-offset-primary-dark transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
