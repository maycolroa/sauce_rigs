const mix = require('laravel-mix');
const glob = require('glob')
const path = require('path')

mix.disableNotifications();

/*
 |--------------------------------------------------------------------------
 | Aliases
 |--------------------------------------------------------------------------
 */

mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.join(__dirname, 'resources/assets'),
            'node_modules': path.join(__dirname, 'node_modules')
        }
    }
})

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
    (glob.sync('resources/assets/' + query) || []).forEach(f => {
        f = f.replace(/[\\\/]+/g, '/');
        cb(f, f.replace('resources/assets', 'public'));
    });
}

const sassOptions = {
    precision: 5
};

// Core javascripts
mixAssetsDir('vendor/js/**/*.js', (src, dest) => mix.scripts(src, dest));

// Fonts
mixAssetsDir('vendor/fonts/*.css', (src, dest) => mix.copy(src, dest));
mixAssetsDir('vendor/fonts/*/*', (src, dest) => mix.copy(src, dest));

/*
 |--------------------------------------------------------------------------
 | Entry point
 |--------------------------------------------------------------------------
 */

mix.js('resources/assets/app.js', 'public');
mix.js('resources/assets/auth.js', 'public');

mix.version();
