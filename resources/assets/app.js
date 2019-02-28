require('./entry-point.js');

import Vue from 'vue'
import router from './router/index.js'
import VueRouterMiddleware from 'vue-router-middleware'
import Notifications from 'vue-notification'

import BootstrapVue from 'bootstrap-vue'

import globals from './globals.js'
import Popper from 'popper.js'

// Required to enable animations on dropdowns/tooltips/popovers
Popper.Defaults.modifiers.computeStyle.gpuAcceleration = false

Vue.config.productionTip = false

Vue.use(BootstrapVue)
Vue.use(Notifications)
Vue.use(VueRouterMiddleware, {
  router,
  middlewares: {
    // Convert to camelcase to dash string ex. requireAuth saves require-auth
    requireAuth(params, to, from, next) {
      // Logic here
      console.log('primero')
      next()
    },
    checkPermission(params, to, from, next) {
      if (params && auth.can[params])
        next()
      else
      {
        console.log('permiso denegado...') 
        next({ path: '/' })
        //next(false)
      }
    }
  }
})

// Global RTL flag
Vue.mixin({
  data: globals
})

Vue.mixin({
  computed: {
    routeAppName: function () {
      
      if (this.$route.path != undefined)
      {
        let name = this.$route.path.split('/')
        return name[1] != '' ? name[1] : ''
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
