import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/View/Components/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                coffee: {
                    950: '#622B14',
                    800: '#7A3B1E',
                    700: '#995F2F',
                    500: '#B97842',
                    300: '#D6B278',
                    200: '#E4D6A9',
                    100: '#F0E6C6',
                    50: '#FBF7EC',
                },
                olive: {
                    700: '#6F6A45',
                    600: '#978F66',
                    200: '#D4D0B1',
                },
                cream: '#F7F0DA',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 18px 45px -24px rgba(98, 43, 20, 0.45)',
            },
        },
    },

    plugins: [forms],
};
