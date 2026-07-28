/**
 * Frontend entry point.
 *
 * This bundle is intentionally lightweight for the first migration step.
 * Page-specific frontend scripts can be imported here gradually as
 * pages move from legacy public assets into the new build pipeline.
 */

window._ = require('lodash');
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfToken = document.head.querySelector('meta[name="csrf-token"]');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

require('./components/frontend-footer-subscribe');
require('./components/frontend-pickers');
require('./components/frontend-hotel-check-price');
require('./components/frontend-loop-swiper');
