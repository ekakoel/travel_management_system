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
    .js('resources/backend/js/admin/panel/index.js', 'public/build/backend/js/admin/panel/index.js')
    .sass('resources/backend/scss/admin/panel/index-entry.scss', 'public/build/backend/css/admin/panel/index.css')
    .js('resources/frontend/js/app.js', 'public/build/frontend/js')
    .sass('resources/frontend/scss/app.scss', 'public/build/frontend/css')
    .sass('resources/frontend/scss/pages/frontend-home-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/landing-page/about/index-entry.scss', 'public/build/frontend/css/pages/about-page-entry.css')
    .sass('resources/frontend/scss/landing-page/contact/index-entry.scss', 'public/build/frontend/css/pages/contact-page-entry.css')
    .sass('resources/frontend/scss/home/profile/index-entry.scss', 'public/build/frontend/css/pages/profile-entry.css')
    .sass('resources/frontend/scss/home/orders/index-entry.scss', 'public/build/frontend/css/pages/frontend-orders-entry.css')
    .sass('resources/frontend/scss/home/manual-book/index-entry.scss', 'public/build/frontend/css/pages/manual-book-entry.css')
    .sass('resources/frontend/scss/pages/auth-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/landing-page/policies/index-entry.scss', 'public/build/frontend/css/pages/public-policy-entry.css')
    .sass('resources/frontend/scss/landing-page/accommodations/index-entry.scss', 'public/build/frontend/css/pages/accommodations-index-entry.css')
    .sass('resources/frontend/scss/landing-page/activities/index-entry.scss', 'public/build/frontend/css/pages/activities-index-entry.css')
    .sass('resources/frontend/scss/landing-page/tours/index-entry.scss', 'public/build/frontend/css/pages/tour-packages-index-entry.css')
    .sass('resources/frontend/scss/landing-page/transports/index-entry.scss', 'public/build/frontend/css/pages/transportations-index-entry.css')
    .sass('resources/frontend/scss/home/booking/hotel-availability-entry.scss', 'public/build/frontend/css/pages/hotel-availability-entry.css')
    .sass('resources/frontend/scss/landing-page/accommodations/detail-entry.scss', 'public/build/frontend/css/pages/accommodation-detail-entry.css')
    .sass('resources/frontend/scss/landing-page/activities/detail-entry.scss', 'public/build/frontend/css/pages/activity-detail-entry.css')
    .sass('resources/frontend/scss/landing-page/tours/detail-entry.scss', 'public/build/frontend/css/pages/tour-detail-entry.css')
    .sass('resources/frontend/scss/landing-page/transports/detail-entry.scss', 'public/build/frontend/css/pages/transport-detail-entry.css')
    .sass('resources/frontend/scss/pages/hotel-booking-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/pages/transport-booking-entry.scss', 'public/build/frontend/css/pages')
    .sass('resources/frontend/scss/home/orders/detail-entry.scss', 'public/build/frontend/css/pages/order-detail-entry.css')
    .js('resources/frontend/js/home/orders/index.js', 'public/build/frontend/js/pages/frontend-orders.js')
    .js('resources/frontend/js/home/manual-book/index.js', 'public/build/frontend/js/pages/manual-book.js')
    .js('resources/frontend/js/pages/auth.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/home/profile/index.js', 'public/build/frontend/js/pages/profile.js')
    .js('resources/frontend/js/landing-page/accommodations/index.js', 'public/build/frontend/js/pages/accommodations-index.js')
    .js('resources/frontend/js/landing-page/activities/index.js', 'public/build/frontend/js/pages/activities-index.js')
    .js('resources/frontend/js/landing-page/transports/index.js', 'public/build/frontend/js/pages/transportations-index.js')
    .js('resources/frontend/js/home/booking/hotel-availability.js', 'public/build/frontend/js/pages/hotel-availability.js')
    .js('resources/frontend/js/landing-page/accommodations/detail.js', 'public/build/frontend/js/pages/accommodation-detail.js')
    .js('resources/frontend/js/landing-page/activities/detail.js', 'public/build/frontend/js/pages/activity-detail.js')
    .js('resources/frontend/js/landing-page/tours/detail.js', 'public/build/frontend/js/pages/tour-detail.js')
    .js('resources/frontend/js/landing-page/tours/index.js', 'public/build/frontend/js/pages/tour-packages-index.js')
    .js('resources/frontend/js/landing-page/transports/detail.js', 'public/build/frontend/js/pages/transport-detail.js')
    .js('resources/frontend/js/pages/hotel-booking.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/home/orders/detail.js', 'public/build/frontend/js/pages/order-detail.js')
    .js('resources/frontend/js/pages/transport-booking.js', 'public/build/frontend/js/pages')
    .js('resources/frontend/js/home/orders/edit.js', 'public/build/frontend/js/pages/order-edit.js')
    .version();

if (!fs.existsSync(ckeditorTarget)) {
    mix.copy(ckeditorSource, ckeditorTarget);
}

mix.disableNotifications();

mix.browserSync({
    proxy: 'http://localhost:8000', // Ganti dengan URL lokal proyek Laravel Anda
});
    
