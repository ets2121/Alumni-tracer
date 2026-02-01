import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6', // Primary Blue
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a', // Deep Navy
                    950: '#172554',
                },
                accent: {
                    500: '#f59e0b', // Gold/Amber
                    600: '#d97706',
                }
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
