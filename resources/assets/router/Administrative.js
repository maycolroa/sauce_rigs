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
    }, {
      name: 'administrative-positions',
      path: 'positions',
      component: () =>
        import('@/views/Administrative/positions/index')
    }, {
      name: 'administrative-positions-create',
      path: 'positions/create',
      component: () =>
        import('@/views/Administrative/positions/create')
    }, {
      name: 'administrative-positions-edit',
      path: 'positions/edit/:id',
      component: () =>
        import('@/views/Administrative/positions/edit')
    }, {
      name: 'administrative-positions-view',
      path: 'positions/view/:id',
      component: () =>
        import('@/views/Administrative/positions/view')
    }, {
      name: 'administrative-regionals',
      path: 'regionals',
      component: () =>
        import('@/views/Administrative/regionals/index')
    }, {
      name: 'administrative-regionals-create',
      path: 'regionals/create',
      component: () =>
        import('@/views/Administrative/regionals/create')
    }, {
      name: 'administrative-regionals-edit',
      path: 'regionals/edit/:id',
      component: () =>
        import('@/views/Administrative/regionals/edit')
    }, {
      name: 'administrative-regionals-view',
      path: 'regionals/view/:id',
      component: () =>
        import('@/views/Administrative/regionals/view')
    }, {
      name: 'administrative-businesses',
      path: 'businesses',
      component: () =>
        import('@/views/Administrative/businesses/index')
    }, {
      name: 'administrative-businesses-create',
      path: 'businesses/create',
      component: () =>
        import('@/views/Administrative/businesses/create')
    }, {
      name: 'administrative-businesses-edit',
      path: 'businesses/edit/:id',
      component: () =>
        import('@/views/Administrative/businesses/edit')
    }, {
      name: 'administrative-businesses-view',
      path: 'businesses/view/:id',
      component: () =>
        import('@/views/Administrative/businesses/view')
    }, {
      name: 'administrative-headquarters',
      path: 'headquarters',
      component: () =>
        import('@/views/Administrative/headquarters/index')
    }, {
      name: 'administrative-headquarters-create',
      path: 'headquarters/create',
      component: () =>
        import('@/views/Administrative/headquarters/create')
    }, {
      name: 'administrative-headquarters-edit',
      path: 'headquarters/edit/:id',
      component: () =>
        import('@/views/Administrative/headquarters/edit')
    }, {
      name: 'administrative-headquarters-view',
      path: 'headquarters/view/:id',
      component: () =>
        import('@/views/Administrative/headquarters/view')
    }, {
      name: 'administrative-areas',
      path: 'areas',
      component: () =>
        import('@/views/Administrative/areas/index')
    }, {
      name: 'administrative-areas-create',
      path: 'areas/create',
      component: () =>
        import('@/views/Administrative/areas/create')
    }, {
      name: 'administrative-areas-edit',
      path: 'areas/edit/:id',
      component: () =>
        import('@/views/Administrative/areas/edit')
    }, {
      name: 'administrative-areas-view',
      path: 'areas/view/:id',
      component: () =>
        import('@/views/Administrative/areas/view')
    }, {
      name: 'administrative-processes',
      path: 'processes',
      component: () =>
        import('@/views/Administrative/processes/index')
    }, {
      name: 'administrative-processes-create',
      path: 'processes/create',
      component: () =>
        import('@/views/Administrative/processes/create')
    }, {
      name: 'administrative-processes-edit',
      path: 'processes/edit/:id',
      component: () =>
        import('@/views/Administrative/processes/edit')
    }, {
      name: 'administrative-processes-view',
      path: 'processes/view/:id',
      component: () =>
        import('@/views/Administrative/processes/view')
    }, {
      name: 'administrative-employees',
      path: 'employees',
      component: () =>
        import('@/views/Administrative/employees/index')
    }, {
      name: 'administrative-employees-create',
      path: 'employees/create',
      component: () =>
        import('@/views/Administrative/employees/create')
    }, {
      name: 'administrative-employees-edit',
      path: 'employees/edit/:id',
      component: () =>
        import('@/views/Administrative/employees/edit')
    }, {
      name: 'administrative-employees-view',
      path: 'employees/view/:id',
      component: () =>
        import('@/views/Administrative/employees/view')
    }, {
      name: 'administrative-employees-informs',
      path: 'employees/informs/:id',
      component: () =>
        import('@/views/Administrative/employees/informs')
    }]
  }]