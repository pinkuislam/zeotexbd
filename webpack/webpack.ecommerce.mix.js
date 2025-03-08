const mix = require('./common.mix');

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

mix.react()
    .js('resources/js/inertia.js', 'js')
    .postCss('resources/css/app.css', 'css', [
        //
    ]);

const publicPath = 'ecommerce-assets/';
mix.setPublicPath('public/ecommerce-assets');
mix.setResourceRoot('../');
mix.webpackConfig({
    output: {
        publicPath,
    },
});
