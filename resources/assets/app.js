require('./entry-point.js');

import Vue from 'vue'
import Notifications from 'vue-notification'
import App from './App'
import router from './router/index.js'

import BootstrapVue from 'bootstrap-vue'

import globals from './globals.js'
import Popper from 'popper.js'

// Required to enable animations on dropdowns/tooltips/popovers
Popper.Defaults.modifiers.computeStyle.gpuAcceleration = false

Vue.config.productionTip = false

Vue.use(BootstrapVue)
Vue.use(Notifications)

// Global RTL flag
Vue.mixin({
  data: globals
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
