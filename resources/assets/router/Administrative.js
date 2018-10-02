import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/administrative',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'administrative',
      component: Home,
    }, {
      name: 'administrative-users',
      path: 'users',
      component: () =>
        import('@/views/Administrative/users/index')
    }, {
      name: 'administrative-users-create',
      path: 'users/creates',
      component: () =>
        import('@/views/Administrative/users/create')
    }, {
      name: 'administrative-users-edit',
      path: 'users/edit/:id',
      component: () =>
        import('@/views/Administrative/users/edit')
    }, {
      name: 'administrative-users-view',
      path: 'users/view/:id',
      component: () =>
        import('@/views/Administrative/users/view')
    }]
  }]