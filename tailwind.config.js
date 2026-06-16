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
                    DEFAULT: '#062D2D',
                    50: '#EAF3F2',
                    100: '#D5E7E6',
                    200: '#A8CFCC',
                    300: '#7BB6B2',
                    400: '#3F8781',
                    500: '#062D2D',
                    600: '#052525',
                    700: '#041F1F',
                    800: '#031818',
                    900: '#021010',
                },
                secondary: {
                    DEFAULT: '#8F6B32',
                    50: '#FBF7EC',
                    100: '#F4E8C9',
                    200: '#E8D092',
                    300: '#D9B65F',
                    400: '#C59B3D',
                    500: '#8F6B32',
                    600: '#755629',
                    700: '#5D4323',
                    800: '#45321D',
                    900: '#2D2115',
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
