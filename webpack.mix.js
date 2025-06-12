const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css')
//     .sourceMaps();

// Compile app.js (JavaScript) from resources to public/js
mix.js('resources/js/app.js', 'public/js')

// Compile app.css (CSS) from resources to public/css
   .postCss('resources/css/app.css', 'public/css', [])
   .sourceMaps();