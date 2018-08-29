require('./entry-point');

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'

import globals from './globals'
import Popper from 'popper.js'

import Notifications from 'vue-notification'

// Required to enable animations on dropdowns/tooltips/popovers
Popper.Defaults.modifiers.computeStyle.gpuAcceleration = false

Vue.config.productionTip = false

Vue.use(BootstrapVue)
Vue.use(Notifications)

// Global RTL flag
Vue.mixin({
  data: globals
});

import Login from './components/Administrative/Auth/LoginComponent.vue';
import MailResetPassword from './components/Administrative/Auth/MailResetPasswordComponent.vue';

/* eslint-disable no-new */
new Vue({
  el: '#app',
  components: { Login,MailResetPassword }
})
