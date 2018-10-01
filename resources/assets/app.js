require('./entry-point.js');
//require('./utils.js');

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

Vue.mixin({
  computed: {
    /*companiesUser: function () {
      return window.globalCompaniesUser !== undefined ? window.globalCompaniesUser : null
    },
    appModulesUser: function () {
      return window.globalAppModulesUser !== undefined ? window.globalAppModulesUser : null
    },*/
    routeAppName: function () {
      
      if (this.$route.name != undefined)
      {
        let name = this.$route.name.split('-')
        return name[0] != '\\' ? name[0] : ''
      }
      
      return ''
    }
  }
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
