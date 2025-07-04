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

window.axios.interceptors.response.use(function (response) {
  // Any status code that lie within the range of 2xx cause this function to trigger
  return response;
}, function (error) {
  console.log(error.response); 

  // Any status codes that falls outside the range of 2xx cause this function to trigger
  // Do something with response error
  if (error.response.status == 401)
  {
    alert("Se cerró la sesión por inactividad.");
    location.reload();
    return;
  }
  else 
    return Promise.reject(error);
});

if (typeof authGlobal !== 'undefined')
  window.auth = authGlobal
else
  window.auth = {}

if (typeof keywords !== 'undefined')
  window.keywords = keywords
else
  window.keywords = {}

if (typeof locationForm !== 'undefined')
  window.locationForm = locationForm
else
  window.locationForm = {}
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

