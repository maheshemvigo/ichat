let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');
mix.js('resources/assets/js/private.js', 'public/js')
mix.js('resources/assets/js/image_upload.js', 'public/js')
mix.js('resources/assets/js/video_upload.js', 'public/js')
mix.js('resources/assets/js/file_upload.js', 'public/js')
mix.js('resources/assets/js/links.js', 'public/js')
mix.js('resources/assets/js/settings.js', 'public/js')
mix.js('resources/assets/js/file.js', 'public/js')