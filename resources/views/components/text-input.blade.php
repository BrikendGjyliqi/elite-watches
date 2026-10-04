@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-transparent border border-text-mint/20 focus:border-accent-gold text-white placeholder-text-mint/40 text-sm px-4 py-2.5 rounded-none shadow-none focus:ring-1 focus:ring-accent-gold focus:outline-none transition']) }}>
