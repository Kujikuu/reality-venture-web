import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.tsx',
        './resources/**/*.ts',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#4d3070',
                secondary: {
                    DEFAULT: '#C88B00',
                    50: '#FFF8E8',
                    100: '#FFEebb',
                    200: '#FFD980',
                    300: '#FFC24D',
                    400: '#E6A319',
                    500: '#C88B00',
                    600: '#A66F00',
                    700: '#855700',
                    800: '#664400',
                    900: '#4A3100',
                },
                'background-light': '#ffffff',
                'background-dark': '#181411',
                surface: '#f5f5f5',
                'text-main': '#181411',
            },
            fontFamily: {
                sans: ['Public Sans', 'Cairo', ...defaultTheme.fontFamily.sans],
                display: ['Public Sans', 'Cairo', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        require('tailwindcss-rtl'),
    ],
};
