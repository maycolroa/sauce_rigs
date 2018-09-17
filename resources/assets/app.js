require('./entry-point.js');

import Vue from 'vue'
import router from './router/index.js'
import Notifications from 'vue-notification'

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

Vue.component('vue-table', require('./components/VueTableComponent.vue'));

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  metaInfo: {
    title: 'SAUCE',
    titleTemplate: '%s - SAUCE'
  },
  updated () {
    // Remove loading state
    setTimeout(() => document.body.classList.remove('app-loading'), 1)
  }
})
