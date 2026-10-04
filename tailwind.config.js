import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    dark: '#2C3531',
                    teal: '#116466',
                },
                // Deeper surfaces for editorial bands (About page, admin)
                surface: {
                    card: '#232C2E',
                    ink: '#1A2124',
                },
                accent: {
                    gold: '#D9B08D',
                    peach: '#FFCB9A',
                },
                text: {
                    mint: '#D1E8E2',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
                accent: ['"Cormorant Garamond"', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};
