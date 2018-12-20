import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/industrialsecure',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'industrialsecure',
      component: Home,
    }, {
      name: 'industrialsecure-activities',
      path: 'activities',
      component: () =>
        import('@/views/IndustrialSecure/activities/index')
    }, {
      name: 'industrialsecure-activities-create',
      path: 'activities/create',
      component: () =>
        import('@/views/IndustrialSecure/activities/create')
    }, {
      name: 'industrialsecure-activities-edit',
      path: 'activities/edit/:id',
      component: () =>
        import('@/views/IndustrialSecure/activities/edit')
    }, {
      name: 'industrialsecure-activities-view',
      path: 'activities/view/:id',
      component: () =>
        import('@/views/IndustrialSecure/activities/view')
    }]
  }]