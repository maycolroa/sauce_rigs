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
    }, {
      name: 'industrialsecure-dangers',
      path: 'dangers',
      component: () =>
        import('@/views/IndustrialSecure/dangers/index')
    }, {
      name: 'industrialsecure-dangers-create',
      path: 'dangers/create',
      component: () =>
        import('@/views/IndustrialSecure/dangers/create')
    }, {
      name: 'industrialsecure-dangers-edit',
      path: 'dangers/edit/:id',
      component: () =>
        import('@/views/IndustrialSecure/dangers/edit')
    }, {
      name: 'industrialsecure-dangers-view',
      path: 'dangers/view/:id',
      component: () =>
        import('@/views/IndustrialSecure/dangers/view')
    }, {
      name: 'industrialsecure-dangermatrix',
      path: 'dangermatrix',
      component: () =>
        import('@/views/IndustrialSecure/dangerMatrix/index')
    }, {
      name: 'industrialsecure-dangermatrix-create',
      path: 'dangermatrix/create',
      component: () =>
        import('@/views/IndustrialSecure/dangerMatrix/create')
    }, {
      name: 'industrialsecure-dangermatrix-edit',
      path: 'dangermatrix/edit/:id',
      component: () =>
        import('@/views/IndustrialSecure/dangerMatrix/edit')
    }, {
      name: 'industrialsecure-dangermatrix-view',
      path: 'dangermatrix/view/:id',
      component: () =>
        import('@/views/IndustrialSecure/dangerMatrix/view')
    }]
  }]