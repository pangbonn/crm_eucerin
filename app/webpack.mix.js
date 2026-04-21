const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [])
    .vue({ version: 3 })
    .js('resources/js/liff/main.js', 'public/js/liff.js')
    .postCss('resources/js/liff/liff.css', 'public/css/liff.css', [
        require('tailwindcss'),
        require('autoprefixer'),
    ]);
