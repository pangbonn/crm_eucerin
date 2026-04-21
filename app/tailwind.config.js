module.exports = {
    content: [
        './resources/js/liff/**/*.{vue,js}',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('daisyui'),
        require('@tailwindcss/forms'),
    ],
    daisyui: {
        themes: ['light'],
    },
};
