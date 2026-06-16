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
                    DEFAULT: '#0a341f',
                    50: '#edf3ef',
                    100: '#d5e4d6',
                    200: '#abcab0',
                    300: '#81b08b',
                    400: '#579665',
                    500: '#0a341f',
                    600: '#082b19',
                    700: '#062312',
                    800: '#041a0c',
                    900: '#021106',
                },
                secondary: {
                    DEFAULT: '#b28538',
                    50: '#f9f4e8',
                    100: '#f2e8d0',
                    200: '#e6d1a1',
                    300: '#d9b973',
                    400: '#cba047',
                    500: '#b28538',
                    600: '#936f2f',
                    700: '#735826',
                    800: '#53411d',
                    900: '#332d13',
                },
                'background-light': '#ffffff',
                'background-dark': '#181411',
                surface: '#f5f5f5',
                'text-main': '#181411',
            },
            fontFamily: {
                sans: ['Inter', 'Cairo', ...defaultTheme.fontFamily.sans],
                display: ['Inter', 'Cairo', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
};
