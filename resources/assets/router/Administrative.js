import LayoutModules from '@/views/layoutModules'
import Home from '@/views/Home'

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
    }, {
      name: 'administrative-roles',
      path: 'roles',
      component: () =>
        import('@/views/Administrative/roles/index')
    }, {
      name: 'administrative-roles-create',
      path: 'roles/create',
      component: () =>
        import('@/views/Administrative/roles/create')
    }, {
      name: 'administrative-roles-edit',
      path: 'roles/edit/:id',
      component: () =>
        import('@/views/Administrative/roles/edit')
    }, {
      name: 'administrative-roles-view',
      path: 'roles/view/:id',
      component: () =>
        import('@/views/Administrative/roles/view')
    }]
  }]