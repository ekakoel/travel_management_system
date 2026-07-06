const mix = require('laravel-mix');
const fs = require('fs');

const ckeditorSource = 'node_modules/@ckeditor/ckeditor5-build-classic/build/ckeditor.js';
const ckeditorTarget = 'public/panel/ckeditor/ckeditor.js';

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

mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 2 })
    .sass('resources/sass/app.scss', 'public/css')
    .js('resources/backend/js/app.js', 'public/build/backend/js')
    .sass('resources/backend/scss/app.scss', 'public/build/backend/css')
    .js('resources/frontend/js/app.js', 'public/build/frontend/js')
    .sass('resources/frontend/scss/app.scss', 'public/build/frontend/css')
    .sass('resources/frontend/scss/pages/frontend-home-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/frontend-orders-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/accommodations-index-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/transportations-index-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/hotel-availability-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/accommodation-detail-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/transport-detail-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/hotel-booking-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/order-detail-entry.scss', 'public/build/frontend/css/pages')
    .js('resources/frontend/js/pages/frontend-orders.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/pages/accommodations-index.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/pages/transportations-index.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/pages/hotel-availability.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/pages/accommodation-detail.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/pages/transport-detail.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/pages/hotel-booking.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/pages/order-detail.js', 'public/build/frontend/js/pages')
    .version();

if (!fs.existsSync(ckeditorTarget)) {
    mix.copy(ckeditorSource, ckeditorTarget);
}

mix.disableNotifications();

mix.browserSync({
    proxy: 'http://localhost:8000', // Ganti dengan URL lokal proyek Laravel Anda
});
    
