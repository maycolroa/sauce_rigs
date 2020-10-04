require('./entry-point.js');

import Vue from 'vue'
import router from './router/index.js'
import VueRouterMiddleware from 'vue-router-middleware'
import Notifications from 'vue-notification'
import VueAnalytics from 'vue-analytics'

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
      next()
    },
    checkPermission(params, to, from, next) {
      let valid = false;

      if (params)
      {
        if (typeof params === 'string')
        {
          if (auth.can[params])
            valid = true;
        }
        else
        {
          for (let value of params)
          {
            if (auth.can[value])
            {
              valid = true;
              break;
            }
          };
        }
      }

      if (valid)
        next();
      else
        next({ path: '/' })
    }
  }
})
Vue.use(VueAnalytics, {
  id: process.env.MIX_GOOGLE_ANALYTICS_ID,
  router,
  /*debug: {
    sendHitTask: process.env.NODE_ENV === 'production'
  }*/
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
  },
  methods: {
    keywordCheck(key, defaultValue = '') {
      return this.keywords[key] != undefined ? this.keywords[key] : defaultValue
    },
    userActivity(description)
    {
      axios.post('/userActivity', { description: description })
      .then(response => {})
      .catch(error => {});
    }
  }
})

Vue.component('vue-table', require('./components/VueTableComponent.vue'));
Vue.component('header-module', require('./views/headerModule.vue'));

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
