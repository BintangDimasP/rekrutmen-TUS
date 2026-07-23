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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    safelist: [
        'bg-purple-800',
        'bg-purple-700',
        'bg-purple-600',
        'bg-purple-100',
        'text-purple-600',
        'text-purple-800',
        'bg-blue-800',
        'bg-blue-100',
        'text-blue-600',
        'bg-red-800',
        'bg-green-800',
        'bg-orange-100',
    ],

    plugins: [forms],
};
