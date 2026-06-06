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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                stormy: {
                    50: '#f0f5fa',
                    100: '#d9e7f3',
                    200: '#bdddfc',
                    300: '#9fcdf0',
                    400: '#88bdf2',
                    500: '#6a89a7',
                    600: '#55708a',
                    700: '#384959',
                    800: '#2a3643',
                    900: '#1c242d',
                },
            },
        },
    },

    plugins: [forms],
};
