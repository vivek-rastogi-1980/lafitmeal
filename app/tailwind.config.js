import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // These category theme classes are built dynamically in Blade (cat-{{ $category }}),
    // so the content scanner never sees them literally. Safelist keeps their rules
    // (which define the --cat colour variable) from being purged.
    safelist: ['cat-veg', 'cat-non_veg', 'cat-vegan'],

    theme: {
        extend: {
            colors: {
                ink: {
                    DEFAULT: '#0b120d',
                    soft: '#101a13',
                    card: '#15221a',
                    line: '#233529',
                },
                cream: {
                    DEFAULT: '#f4f1e4',
                    dim: '#b8bfae',
                },
                lime: {
                    neon: '#c6f24e',
                    deep: '#8fc428',
                },
                coral: '#ff7a59',
                mint: '#4adeb0',
                gold: '#f2c94c',
            },
            fontFamily: {
                display: ['Unbounded', 'system-ui', 'sans-serif'],
                sans: ['"Space Grotesk"', ...defaultTheme.fontFamily.sans],
                accent: ['"Instrument Serif"', 'Georgia', 'serif'],
            },
            animation: {
                marquee: 'marquee 28s linear infinite',
                float: 'float 7s ease-in-out infinite',
                'spin-slow': 'spin 14s linear infinite',
                'pulse-glow': 'pulseGlow 3.5s ease-in-out infinite',
            },
            keyframes: {
                marquee: {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0) rotate(-2deg)' },
                    '50%': { transform: 'translateY(-18px) rotate(3deg)' },
                },
                pulseGlow: {
                    '0%, 100%': { boxShadow: '0 0 24px 0 rgba(198,242,78,.25)' },
                    '50%': { boxShadow: '0 0 64px 6px rgba(198,242,78,.45)' },
                },
            },
        },
    },

    plugins: [forms],
};
