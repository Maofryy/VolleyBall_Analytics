let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

mix.js('public/vuejs/src/app.js', './dist/').vue();
mix.js('public/vuejs/src/test/test.js', './dist/').vue();
mix.js('public/vuejs/src/player/players-list.js', './dist/').vue();
mix.js('public/vuejs/src/player/player-info.js', './dist/').vue();
mix.setResourceRoot('../');