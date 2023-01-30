import Vue from 'vue'
import Router from 'vue-router'
import { middleware } from 'vue-router-middleware'
import Meta from 'vue-meta'
import LayoutMaster from '@/views/layoutMaster'
import IndexPage from '@/views/indexPage'
import ChangePassword from '@/views/Administrative/users/changePassword'
import DefaultModule from '@/views/Administrative/users/defaultModule'
import TermsCondition from '@/views/Administrative/users/termsConditions'
import Firm from '@/views/Administrative/users/firm'
import UserActivity from '@/views/Administrative/userActivity/index'
import Alerts from '@/utils/Alerts.js';

import globals from '@/globals'

// rutas
import Administrative from './Administrative.js'
import IndustrialHygiene from './IndustrialHygiene.js'
import IndustrialSecure from './IndustrialSecure.js'
import LegalAspects from './LegalAspects.js'
import MeasurementMonitoring from './MeasurementMonitoring.js'
import PreventiveOccupationalMedicine from './PreventiveOccupationalMedicine.js'
import TrainingQualification from './TrainingQualification.js'
import System from './System.js'

Vue.use(Router)
Vue.use(Meta)

const router = new Router({
  base: '/',
  mode: 'history',
  routes: [
    ...middleware('require-auth', [
      { 
        path: '', 
        component: LayoutMaster,
        children: [
          {
            path: '',
            name: '',
            component: IndexPage
          },
          {
            path: 'changepassword',
            name: 'changepassword',
            component: ChangePassword
          },
          {
            path: 'defaultmodule',
            name: 'defaultmodule',
            component: DefaultModule
          },
          {
            name: 'termsconditions',
            path: 'termsconditions',
            component: TermsCondition
          },
          {
            name: 'firm',
            path: 'firm',
            component: Firm
          },
          {
            name: 'useractivitymonitoring',
            path: 'useractivitymonitoring',
            component: UserActivity
          },
        ]
          .concat(Administrative) 
          .concat(IndustrialHygiene)
          .concat(IndustrialSecure)
          .concat(LegalAspects)
          .concat(MeasurementMonitoring)
          .concat(PreventiveOccupationalMedicine)
          .concat(TrainingQualification)    
          .concat(System)    
      }
    ])
  ]
})

router.afterEach(() => {
  // On small screens collapse sidenav
  if (window.layoutHelpers && window.layoutHelpers.isSmallScreen() && !window.layoutHelpers.isCollapsed()) {
    setTimeout(() => window.layoutHelpers.setCollapsed(true, true), 10)
  }

  // Scroll to top of the page
  globals().scrollTop(0, 0)
})

router.beforeEach((to, from, next) => {
  //session expired
  /*axios.interceptors.response.use(function (response) {
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    return response;
    }, function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error
    if(error.response.status === 401 || error.response.status === 419) {
      console.log('error')
        // redirect to login page
        Alerts.error('Error', 'Su sesión ha expirado, para continuar abra otra pestaña e inicie sesion en esta');
    }
    return Promise.reject(error);
  })*/
  // Set loading state
  document.body.classList.add('app-loading')

  // Add tiny timeout to finish page transition
  setTimeout(() => next(), 10)
})

export default router
