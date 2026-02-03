import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#1b4332', // Primary (Deep Green)
                    600: '#166534',
                    700: '#14532d',
                    800: '#064e3b',
                    900: '#022c22',
                    950: '#011c15',
                },
                primary: {
                    DEFAULT: '#1b4332',
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#1b4332',
                    600: '#166534',
                    700: '#14532d',
                    800: '#064e3b',
                    900: '#022c22',
                },
                accent: {
                    DEFAULT: '#d4af37', // Gold
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#d4af37',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                secondary: {
                    DEFAULT: '#d4af37',
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#d4af37',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                dark: {
                    bg: {
                        DEFAULT: '#0f172a',   // main app background
                        deep: '#020617',      // deepest background
                        elevated: '#1e293b',  // cards, modals
                        subtle: '#16202a',    // sections
                    },

                    text: {
                        primary: '#f8fafc',   // main text
                        secondary: '#e5e7eb', // sub text
                        muted: '#cbd5e1',     // muted text
                        disabled: '#94a3b8',  // disabled
                    },

                    border: {
                        DEFAULT: '#334155',   // normal borders
                        subtle: '#475569',    // soft borders
                    },

                    state: {
                        hover: '#334155',
                        active: '#475569',
                    },
                },

                surface: {
                    main: 'var(--bg-main)',
                    card: 'var(--bg-card)',
                },
                content: {
                    main: 'var(--text-main)',
                    muted: 'var(--text-muted)',
                },
                border: 'var(--border-color)',
            },
            boxShadow: {
                'morphoric': '0 32px 64px -12px rgba(0, 0, 0, 0.08)',
            },
            backdropBlur: {
                '3xl': '64px',
            }
        },
    },

    plugins: [forms],
};
