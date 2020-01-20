
window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.jQuery = window.$ = require('jquery');
window.jQueryUi = require('jquery-ui');
window.jQueryEasing = require('jquery.easing');
window.popperJs = require('popper.js');
window.bootstrap = require('bootstrap');
window.Trianglify = require('trianglify');
window.owlCarousel = require('owl.carousel');
window.ClassicEditor = require('ckeditor');
window.videojs = require('video.js');
//window.Sentry = require('@sentry/node');

require('../../public_html/js/vote');
require('../../public_html/js/infiniteScrolling');
require('../../public_html/js/subscribe');
require('../../public_html/js/script');
