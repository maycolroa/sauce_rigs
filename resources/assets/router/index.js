import Vue from 'vue'
import Router from 'vue-router'
import { middleware } from 'vue-router-middleware'
import Meta from 'vue-meta'
import LayoutMaster from '@/views/layoutMaster'
import IndexPage from '@/views/indexPage'
import ChangePassword from '@/views/Administrative/users/changePassword'

import globals from '@/globals'

// rutas
import Administrative from './Administrative.js'
import IndustrialHygiene from './IndustrialHygiene.js'
import IndustrialSecure from './IndustrialSecure.js'
import LegalAspects from './LegalAspects.js'
import MeasurementMonitoring from './MeasurementMonitoring.js'
import PreventiveOccupationalMedicine from './PreventiveOccupationalMedicine.js'
import TrainingQualification from './TrainingQualification.js'

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
        ]
          .concat(Administrative) 
          .concat(IndustrialHygiene)
          .concat(IndustrialSecure)
          .concat(LegalAspects)
          .concat(MeasurementMonitoring)
          .concat(PreventiveOccupationalMedicine)
          .concat(TrainingQualification)    
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
  // Set loading state
  document.body.classList.add('app-loading')

  // Add tiny timeout to finish page transition
  setTimeout(() => next(), 10)
})

export default router
