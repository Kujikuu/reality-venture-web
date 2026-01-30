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
                primary: {
                    DEFAULT: '#4d3070',
                    50: '#F3EDF8',
                    100: '#E4D7F0',
                    200: '#CBB2E1',
                    300: '#B08BD2',
                    400: '#9564C3',
                    500: '#4d3070',
                    600: '#623194',
                    700: '#4D2574',
                    800: '#391A55',
                    900: '#261035',
                },
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
