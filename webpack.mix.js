const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 3 })  // Enable Vue 3 support
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'), // Enables @import in CSS
    ]);
