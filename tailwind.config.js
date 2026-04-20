import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                title: ['Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    DEFAULT: '#BD1613', // Principal
                    hover: '#910C09', // Botões hover
                    accent: '#910C09', // Texto accent
                    icon: '#271818', // Background pills/badges vermelhas
                },
                whatsapp: '#0B8F42',
                surface: {
                    DEFAULT: '#131416', // Cards dark
                    accent: '#272525', // Cards acentuados
                },
                ink: {
                    DEFAULT: '#FAFBFC', // Texto principal
                    muted: '#A3A3A3', // Texto hover / secundário
                    subtle: '#6B6B6B', // Texto terciário
                },
            },
            backgroundColor: {
                page: '#000000', // Background principal
            },
            borderColor: {
                subtle: 'rgba(255,255,255,0.07)',
            },
            boxShadow: {
                card: '0 1px 3px rgba(0,0,0,0.4), 0 1px 2px rgba(0,0,0,0.6)',
                'card-hover': '0 4px 16px rgba(0,0,0,0.5)',
                brand: '0 0 20px rgba(189,22,19,0.25)',
            },
            backgroundImage: {
                'brand-gradient': 'linear-gradient(135deg, #BD1613, #910C09)',
                'brand-subtle': 'linear-gradient(135deg, rgba(189,22,19,0.15), rgba(145,12,9,0.05))',
            },
            animation: {
                'fade-in': 'fadeIn 0.2s ease-out',
                'slide-up': 'slideUp 0.25s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [],
};