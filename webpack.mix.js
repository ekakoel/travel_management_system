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
    .sass('resources/backend/scss/admin/dashboard/index-entry.scss', 'public/build/backend/css/admin/dashboard/index.css')
    .js('resources/backend/js/admin/currency/index.js', 'public/build/backend/js/admin/currency/index.js')
    .sass('resources/backend/scss/admin/currency/index-entry.scss', 'public/build/backend/css/admin/currency/index.css')
    .js('resources/backend/js/admin/users/manager.js', 'public/build/backend/js/admin/users/manager.js')
    .sass('resources/backend/scss/admin/users/manager-entry.scss', 'public/build/backend/css/admin/users/manager.css')
    .js('resources/backend/js/admin/terms/index.js', 'public/build/backend/js/admin/terms/index.js')
    .sass('resources/backend/scss/admin/terms/index-entry.scss', 'public/build/backend/css/admin/terms/index.css')
    .js('resources/backend/js/admin/company-profile/edit.js', 'public/build/backend/js/admin/company-profile/edit.js')
    .sass('resources/backend/scss/admin/company-profile/edit-entry.scss', 'public/build/backend/css/admin/company-profile/edit.css')
    .js('resources/backend/js/admin/footer-manager/index.js', 'public/build/backend/js/admin/footer-manager/index.js')
    .sass('resources/backend/scss/admin/footer-manager/index-entry.scss', 'public/build/backend/css/admin/footer-manager/index.css')
    .js('resources/backend/js/admin/reviews/index.js', 'public/build/backend/js/admin/reviews/index.js')
    .sass('resources/backend/scss/admin/reviews/index-entry.scss', 'public/build/backend/css/admin/reviews/index.css')
    .js('resources/backend/js/operations/transport-management/index.js', 'public/build/backend/js/operations/transport-management/index.js')
    .sass('resources/backend/scss/operations/transport-management/index-entry.scss', 'public/build/backend/css/operations/transport-management/index.css')
    .js('resources/backend/js/operations/transport-management/detail.js', 'public/build/backend/js/operations/transport-management/detail.js')
    .sass('resources/backend/scss/operations/transport-management/detail-entry.scss', 'public/build/backend/css/operations/transport-management/detail.css')
    .js('resources/backend/js/operations/orders-admin/index.js', 'public/build/backend/js/operations/orders-admin/index.js')
    .js('resources/backend/js/operations/orders-admin/create-hotel-order.js', 'public/build/backend/js/operations/orders-admin/create-hotel-order.js')
    .sass('resources/backend/scss/operations/orders-admin/index-entry.scss', 'public/build/backend/css/operations/orders-admin/index.css')
    .sass('resources/backend/scss/operations/orders-admin/detail-entry.scss', 'public/build/backend/css/operations/orders-admin/detail.css')
    .js('resources/backend/js/operations/guides/index.js', 'public/build/backend/js/operations/guides/index.js')
    .sass('resources/backend/scss/operations/guides/index-entry.scss', 'public/build/backend/css/operations/guides/index.css')
    .js('resources/backend/js/operations/drivers/index.js', 'public/build/backend/js/operations/drivers/index.js')
    .sass('resources/backend/scss/operations/drivers/index-entry.scss', 'public/build/backend/css/operations/drivers/index.css')
    .js('resources/backend/js/operations/activities/index.js', 'public/build/backend/js/operations/activities/index.js')
    .sass('resources/backend/scss/operations/activities/index-entry.scss', 'public/build/backend/css/operations/activities/index.css')
    .js('resources/backend/js/operations/activities/forms.js', 'public/build/backend/js/operations/activities/forms.js')
    .sass('resources/backend/scss/operations/activities/forms-entry.scss', 'public/build/backend/css/operations/activities/forms.css')
    .js('resources/backend/js/operations/tours/index.js', 'public/build/backend/js/operations/tours/index.js')
    .sass('resources/backend/scss/operations/tours/index-entry.scss', 'public/build/backend/css/operations/tours/index.css')
    .js('resources/backend/js/operations/tours/detail.js', 'public/build/backend/js/operations/tours/detail.js')
    .sass('resources/backend/scss/operations/tours/detail-entry.scss', 'public/build/backend/css/operations/tours/detail.css')
    .js('resources/backend/js/operations/tours/forms.js', 'public/build/backend/js/operations/tours/forms.js')
    .sass('resources/backend/scss/operations/tours/forms-entry.scss', 'public/build/backend/css/operations/tours/forms.css')
    .js('resources/backend/js/operations/transports/index.js', 'public/build/backend/js/operations/transports/index.js')
    .sass('resources/backend/scss/operations/transports/index-entry.scss', 'public/build/backend/css/operations/transports/index.css')
    .js('resources/backend/js/operations/transports/detail.js', 'public/build/backend/js/operations/transports/detail.js')
    .sass('resources/backend/scss/operations/transports/detail-entry.scss', 'public/build/backend/css/operations/transports/detail.css')
    .js('resources/backend/js/operations/transports/forms.js', 'public/build/backend/js/operations/transports/forms.js')
    .sass('resources/backend/scss/operations/transports/forms-entry.scss', 'public/build/backend/css/operations/transports/forms.css')
    .js('resources/backend/js/operations/reservations/index.js', 'public/build/backend/js/operations/reservations/index.js')
    .sass('resources/backend/scss/operations/reservations/index-entry.scss', 'public/build/backend/css/operations/reservations/index.css')
    .js('resources/backend/js/operations/reservations/detail.js', 'public/build/backend/js/operations/reservations/detail.js')
    .sass('resources/backend/scss/operations/reservations/detail-entry.scss', 'public/build/backend/css/operations/reservations/detail.css')
    .js('resources/backend/js/finance/invoices/detail.js', 'public/build/backend/js/finance/invoices/detail.js')
    .sass('resources/backend/scss/finance/invoices/detail-entry.scss', 'public/build/backend/css/finance/invoices/detail.css')
    .js('resources/backend/js/finance/invoices/index.js', 'public/build/backend/js/finance/invoices/index.js')
    .sass('resources/backend/scss/finance/invoices/index-entry.scss', 'public/build/backend/css/finance/invoices/index.css')
    .js('resources/backend/js/operations/hotels/index.js', 'public/build/backend/js/operations/hotels/index.js')
    .sass('resources/backend/scss/operations/hotels/index-entry.scss', 'public/build/backend/css/operations/hotels/index.css')
    .js('resources/backend/js/operations/hotels/detail.js', 'public/build/backend/js/operations/hotels/detail.js')
    .sass('resources/backend/scss/operations/hotels/detail-entry.scss', 'public/build/backend/css/operations/hotels/detail.css')
    .js('resources/backend/js/operations/hotels/forms.js', 'public/build/backend/js/operations/hotels/forms.js')
    .sass('resources/backend/scss/operations/hotels/forms-entry.scss', 'public/build/backend/css/operations/hotels/forms.css')
    .js('resources/frontend/js/app.js', 'public/build/frontend/js')
    .sass('resources/frontend/scss/app.scss', 'public/build/frontend/css')
    .js('resources/frontend/js/components/frontend-pickers.js', 'public/build/frontend/js/components/frontend-pickers.js')
    .sass('resources/frontend/scss/components/frontend-pickers.scss', 'public/build/frontend/css/components/frontend-pickers.css')
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
    
