const mix = require('laravel-mix');
const webpackConfig = require('./webpack.config');

mix.options({
    legacyNodePolyfills: true,
    // processCssUrls: false,
});

mix.webpackConfig(webpackConfig);

// mix.webpackConfig({
//     stats: {
//         children: true,
//     },
// });

if (!mix.inProduction()) {
    mix.sourceMaps(false, 'source-map');
}

mix.version();
mix.disableSuccessNotifications();


module.exports = mix;
