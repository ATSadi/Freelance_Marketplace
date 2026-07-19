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
                display: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                accent: {
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#7c3aed',
                },
                ink: {
                    900: '#0b1020',
                    800: '#141a2e',
                    700: '#1e2540',
                },
            },
            boxShadow: {
                soft: '0 1px 2px rgba(16,24,40,0.04), 0 4px 16px rgba(16,24,40,0.06)',
                card: '0 1px 3px rgba(16,24,40,0.06), 0 12px 32px -12px rgba(79,70,229,0.20)',
                glow: '0 10px 40px -10px rgba(124,58,237,0.55)',
                'glow-lg': '0 24px 70px -20px rgba(99,102,241,0.6)',
            },
            backgroundImage: {
                'brand-gradient': 'linear-gradient(135deg, #6366f1 0%, #7c3aed 55%, #4f46e5 100%)',
                'brand-radial': 'radial-gradient(1200px circle at 15% 15%, rgba(99,102,241,0.14), transparent 45%), radial-gradient(1000px circle at 85% 10%, rgba(124,58,237,0.12), transparent 40%)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                float: {
                    '0%,100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                shimmer: {
                    '100%': { transform: 'translateX(100%)' },
                },
                'gradient-pan': {
                    '0%,100%': { 'background-position': '0% 50%' },
                    '50%': { 'background-position': '100% 50%' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.6s cubic-bezier(0.22,1,0.36,1) both',
                'fade-in': 'fade-in 0.7s ease both',
                'scale-in': 'scale-in 0.4s cubic-bezier(0.22,1,0.36,1) both',
                float: 'float 6s ease-in-out infinite',
                'gradient-pan': 'gradient-pan 8s ease infinite',
            },
        },
    },

    plugins: [forms],
};
