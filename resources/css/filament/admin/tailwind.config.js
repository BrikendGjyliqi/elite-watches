import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './app/Providers/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            // Luxury is sharp, not bubbly: flatten Filament's radii across every component.
            borderRadius: {
                sm: '2px',
                DEFAULT: '2px',
                md: '3px',
                lg: '4px',
                xl: '4px',
                '2xl': '4px',
            },
            fontFamily: {
                serif: ['"Playfair Display"', 'Georgia', 'serif'],
                accent: ['"Cormorant Garamond"', 'Georgia', 'serif'],
            },
            colors: {
                gold: '#D9B08D',
                peach: '#FFCB9A',
                mint: '#D1E8E2',
                rose: '#E8A598',
                jade: '#7FD1A8',
                teal: '#116466',
                vault: '#2C3531',
            },
        },
    },
}
