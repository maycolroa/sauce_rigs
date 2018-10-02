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
      path: 'users/create',
      component: Home,
    }, {
      name: 'administrative-users-edit',
      path: 'users/edit/:id',
      component: Home,
    }, {
      name: 'administrative-users-view',
      path: 'users/view/:id',
      component: Home,
    }]
  }]