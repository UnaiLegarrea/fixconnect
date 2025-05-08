import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: 'class', // Activamos el modo oscuro basado en clase

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#1A2B4B',
                    light: '#2A3C6C',
                    dark: '#0A1A3A',
                },
                secondary: {
                    DEFAULT: '#4B8BF5',
                    light: '#6CA5FF',
                    dark: '#3A70D9',
                },
                accent: {
                    DEFAULT: '#FF6B35',
                    light: '#FF8C5A',
                    dark: '#E55020',
                },
                neutral: {
                    DEFAULT: '#F5F5F5',
                    dark: '#E0E0E0',
                    blue: "#14213D",
                },
                success: '#28A745',
                warning: '#FFC107',
                error: '#DC3545',
                info: '#17A2B8',
                dark: {
                    primary: '#121212',
                    secondary: '#1E1E1E',
                    surface: '#282828',
                    card: '#2D2D2D'
                },
            },
        },
    },

    plugins: [forms],
};
