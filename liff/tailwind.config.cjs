/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ['./index.html', './src/**/*.{vue,js}'],
    theme: { extend: {} },
    plugins: [
        require('daisyui'),
        require('@tailwindcss/forms'),
    ],
    daisyui: {
        themes: [
            {
                eucerin: {
                    'primary':          '#E2001A',
                    'primary-content':  '#ffffff',
                    'secondary':        '#9B1B2A',
                    'secondary-content':'#ffffff',
                    'accent':           '#F5A0A8',
                    'accent-content':   '#3D0008',
                    'neutral':          '#2B2B2B',
                    'neutral-content':  '#ffffff',
                    'base-100':         '#ffffff',
                    'base-200':         '#F8F8F8',
                    'base-300':         '#E8E8E8',
                    'base-content':     '#1C1C1C',
                    'info':             '#3ABFF8',
                    'success':          '#36D399',
                    'warning':          '#FBBD23',
                    'error':            '#E2001A',
                },
            },
        ],
    },
};
