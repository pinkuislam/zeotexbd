const path = require('path')

// https://stefanbauer.me/tips-and-tricks/autocompletion-for-webpack-path-aliases-in-phpstorm-when-using-laravel-mix
module.exports = {
    resolve: {
        symlinks: false,
        alias: {
            ziggy: path.resolve(__dirname, '../vendor/tightenco/ziggy/dist'),
        },
        // extensions: ['.js', '.jsx', '.vue', '.json'],
    },
    devServer: {
        allowedHosts: 'all',
    },
}
