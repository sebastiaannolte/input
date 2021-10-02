const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

module.exports = {
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.gray,
            red: colors.red,
            yellow: colors.amber,
            blue: colors.sky,
            indigo: colors.sky,
            green: colors.green,
        },
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                'inter': ['"Inter"', 'sans-serif']
            },
            spacing: {
                '120': '33rem',
            },
            minHeight: {
                '20': '5rem',
            }

        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
            fontWeight: ['first'],
            fontSize: ['first'],
        },
    },

    plugins: [require('@tailwindcss/forms')],
};