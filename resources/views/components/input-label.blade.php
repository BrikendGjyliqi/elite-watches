@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs uppercase tracking-[0.15em] text-text-mint/70 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
