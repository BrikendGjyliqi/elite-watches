{{-- Tiny inline flag for a maison's country of origin. Unknown countries render nothing. --}}
@props(['country'])

@switch(strtolower((string) $country))
    @case('switzerland')
        <svg {{ $attributes->merge(['class' => 'h-4 w-4 shrink-0']) }} viewBox="0 0 32 32" role="img" aria-label="Switzerland">
            <rect width="32" height="32" fill="#D52B1E" />
            <path d="M13 6h6v7h7v6h-7v7h-6v-7H6v-6h7z" fill="#fff" />
        </svg>
        @break
    @case('france')
        <svg {{ $attributes->merge(['class' => 'h-3 w-[18px] shrink-0']) }} viewBox="0 0 3 2" role="img" aria-label="France">
            <rect width="1" height="2" fill="#0055A4" /><rect x="1" width="1" height="2" fill="#fff" /><rect x="2" width="1" height="2" fill="#EF4135" />
        </svg>
        @break
    @case('italy')
        <svg {{ $attributes->merge(['class' => 'h-3 w-[18px] shrink-0']) }} viewBox="0 0 3 2" role="img" aria-label="Italy">
            <rect width="1" height="2" fill="#009246" /><rect x="1" width="1" height="2" fill="#fff" /><rect x="2" width="1" height="2" fill="#CE2B37" />
        </svg>
        @break
    @case('germany')
        <svg {{ $attributes->merge(['class' => 'h-3 w-[18px] shrink-0']) }} viewBox="0 0 5 3" role="img" aria-label="Germany">
            <rect width="5" height="1" fill="#000" /><rect y="1" width="5" height="1" fill="#DD0000" /><rect y="2" width="5" height="1" fill="#FFCE00" />
        </svg>
        @break
    @case('japan')
        <svg {{ $attributes->merge(['class' => 'h-3 w-[18px] shrink-0']) }} viewBox="0 0 3 2" role="img" aria-label="Japan">
            <rect width="3" height="2" fill="#fff" /><circle cx="1.5" cy="1" r="0.6" fill="#BC002D" />
        </svg>
        @break
@endswitch
