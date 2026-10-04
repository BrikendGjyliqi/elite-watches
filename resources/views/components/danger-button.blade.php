<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600/90 border border-transparent font-sans text-xs text-white uppercase tracking-[0.2em] hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-primary-dark transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
