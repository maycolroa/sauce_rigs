import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'
import { middleware } from 'vue-router-middleware'

export default [{
    path: '/administrative',
    component: LayoutModules,
    children: [
      {
        path: '',
        name: 'administrative',
        component: Home,
      },
      ...middleware({ 'check-permission': 'users_r' }, [
        {
          name: 'administrative-users',
          path: 'users',
          component: () =>
            import('@/views/Administrative/users/index')
        }
      ]),
      ...middleware({ 'check-permission': 'users_c' }, [
        {
          name: 'administrative-users-create',
          path: 'users/create',
          component: () =>
            import('@/views/Administrative/users/create')
        }
      ]),
      ...middleware({ 'check-permission': 'users_u' }, [
        {
          name: 'administrative-users-edit',
          path: 'users/edit/:id',
          component: () =>
            import('@/views/Administrative/users/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'users_r' }, [
        {
          name: 'administrative-users-view',
          path: 'users/view/:id',
          component: () =>
            import('@/views/Administrative/users/view')
        }
      ]),
      ...middleware({ 'check-permission': 'roles_r' }, [
        {
          name: 'administrative-roles',
          path: 'roles',
          component: () =>
            import('@/views/Administrative/roles/index')
        }
      ]),
      ...middleware({ 'check-permission': 'roles_c' }, [
      {
        name: 'administrative-roles-create',
        path: 'roles/create',
        component: () =>
          import('@/views/Administrative/roles/create')
      }]),
      ...middleware({ 'check-permission': 'roles_u' }, [
        {
          name: 'administrative-roles-edit',
          path: 'roles/edit/:id',
          component: () =>
            import('@/views/Administrative/roles/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roles_r' }, [
        {
          name: 'administrative-roles-view',
          path: 'roles/view/:id',
          component: () =>
            import('@/views/Administrative/roles/view')
        }
      ]),
      ...middleware({ 'check-permission': 'positions_r' }, [
        {
          name: 'administrative-positions',
          path: 'positions',
          component: () =>
            import('@/views/Administrative/positions/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'positions_c' }, [
        {
          name: 'administrative-positions-create',
          path: 'positions/create',
          component: () =>
            import('@/views/Administrative/positions/create')
        }
      ]),
      ...middleware({ 'check-permission': 'positions_u' }, [
        {
          name: 'administrative-positions-edit',
          path: 'positions/edit/:id',
          component: () =>
            import('@/views/Administrative/positions/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'positions_r' }, [
        {
          name: 'administrative-positions-view',
          path: 'positions/view/:id',
          component: () =>
            import('@/views/Administrative/positions/view')
        }
      ]),
      ...middleware({ 'check-permission': 'regionals_r' }, [
        {
          name: 'administrative-regionals',
          path: 'regionals',
          component: () =>
            import('@/views/Administrative/regionals/index')
        }
      ]),
      ...middleware({ 'check-permission': 'regionals_c' }, [
        {
          name: 'administrative-regionals-create',
          path: 'regionals/create',
          component: () =>
            import('@/views/Administrative/regionals/create')
        }
      ]),
      ...middleware({ 'check-permission': 'regionals_u' }, [
        {
          name: 'administrative-regionals-edit',
          path: 'regionals/edit/:id',
          component: () =>
            import('@/views/Administrative/regionals/edit')
        }
      ]), 
      ...middleware({ 'check-permission': 'regionals_r' }, [
        {
          name: 'administrative-regionals-view',
          path: 'regionals/view/:id',
          component: () =>
            import('@/views/Administrative/regionals/view')
        }
      ]),
      ...middleware({ 'check-permission': 'businesses_r' }, [
        {
          name: 'administrative-businesses',
          path: 'businesses',
          component: () =>
            import('@/views/Administrative/businesses/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'businesses_c' }, [
        {
          name: 'administrative-businesses-create',
          path: 'businesses/create',
          component: () =>
            import('@/views/Administrative/businesses/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'businesses_u' }, [
        {
          name: 'administrative-businesses-edit',
          path: 'businesses/edit/:id',
          component: () =>
            import('@/views/Administrative/businesses/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'businesses_r' }, [
        {
          name: 'administrative-businesses-view',
          path: 'businesses/view/:id',
          component: () =>
            import('@/views/Administrative/businesses/view')
        }
      ]),
      ...middleware({ 'check-permission': 'headquarters_r' }, [
        {
          name: 'administrative-headquarters',
          path: 'headquarters',
          component: () =>
            import('@/views/Administrative/headquarters/index')
        }
      ]),
      ...middleware({ 'check-permission': 'headquarters_c' }, [
        {
          name: 'administrative-headquarters-create',
          path: 'headquarters/create',
          component: () =>
            import('@/views/Administrative/headquarters/create')
        }
      ]),
      ...middleware({ 'check-permission': 'headquarters_u' }, [
        {
          name: 'administrative-headquarters-edit',
          path: 'headquarters/edit/:id',
          component: () =>
            import('@/views/Administrative/headquarters/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'headquarters_r' }, [
        {
          name: 'administrative-headquarters-view',
          path: 'headquarters/view/:id',
          component: () =>
            import('@/views/Administrative/headquarters/view')
        }
      ]),
      ...middleware({ 'check-permission': 'areas_r' }, [
        {
          name: 'administrative-areas',
          path: 'areas',
          component: () =>
            import('@/views/Administrative/areas/index')
        }
      ]),
      ...middleware({ 'check-permission': 'areas_c' }, [
        {
          name: 'administrative-areas-create',
          path: 'areas/create',
          component: () =>
            import('@/views/Administrative/areas/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'areas_u' }, [
        {
          name: 'administrative-areas-edit',
          path: 'areas/edit/:id',
          component: () =>
            import('@/views/Administrative/areas/edit')
        }
      ]), 
      ...middleware({ 'check-permission': 'areas_r' }, [
        {
          name: 'administrative-areas-view',
          path: 'areas/view/:id',
          component: () =>
            import('@/views/Administrative/areas/view')
        }
      ]), 
      ...middleware({ 'check-permission': 'processes_r' }, [
        {
          name: 'administrative-processes',
          path: 'processes',
          component: () =>
            import('@/views/Administrative/processes/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'processes_c' }, [
        {
          name: 'administrative-processes-create',
          path: 'processes/create',
          component: () =>
            import('@/views/Administrative/processes/create')
        }
      ]),
      ...middleware({ 'check-permission': 'processes_u' }, [
        {
          name: 'administrative-processes-edit',
          path: 'processes/edit/:id',
          component: () =>
            import('@/views/Administrative/processes/edit')
        }
      ]), 
      ...middleware({ 'check-permission': 'processes_r' }, [
        {
          name: 'administrative-processes-view',
          path: 'processes/view/:id',
          component: () =>
            import('@/views/Administrative/processes/view')
        }
      ]), 
      ...middleware({ 'check-permission': 'employees_r' }, [
        {
          name: 'administrative-employees',
          path: 'employees',
          component: () =>
            import('@/views/Administrative/employees/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'employees_c' }, [
        {
          name: 'administrative-employees-create',
          path: 'employees/create',
          component: () =>
            import('@/views/Administrative/employees/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'employees_u' }, [
        {
          name: 'administrative-employees-edit',
          path: 'employees/edit/:id',
          component: () =>
            import('@/views/Administrative/employees/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'employees_r' }, [
        {
          name: 'administrative-employees-view',
          path: 'employees/view/:id',
          component: () =>
            import('@/views/Administrative/employees/view')
        }
      ]),
      ...middleware({ 'check-permission': 'configurations_r' }, [
        {
          name: 'administrative-configurations',
          path: 'configurations',
          component: () =>
            import('@/views/Administrative/configurations/index')
        }
      ]),
      ...middleware({ 'check-permission': 'actionPlans_r' }, [
        {
          name: 'administrative-actionplans',
          path: 'actionplans',
          component: () =>
            import('@/views/Administrative/actionplans/index')
        }
      ]),
      ...middleware({ 'check-permission': 'actionPlans_u' }, [
        {
          name: 'administrative-actionplans-edit',
          path: 'actionplans/edit/:id',
          component: () =>
            import('@/views/Administrative/actionplans/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'logos_r' }, [
        {
          name: 'administrative-logos',
          path: 'logos',
          component: () =>
            import('@/views/Administrative/logos/index')
        }
      ]),
      ...middleware({ 'check-permission': 'customLabels_r' }, [
        {
          name: 'administrative-customlabels',
          path: 'customlabels',
          component: () =>
            import('@/views/Administrative/labels/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'customLabels_c' }, [
        {
          name: 'administrative-customlabels-create',
          path: 'customlabels/create',
          component: () =>
            import('@/views/Administrative/labels/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'customLabels_u' }, [
        {
          name: 'administrative-customlabels-edit',
          path: 'customlabels/edit/:id',
          component: () =>
            import('@/views/Administrative/labels/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'customLabels_r' }, [
        {
          name: 'administrative-customlabels-view',
          path: 'customlabels/view/:id',
          component: () =>
            import('@/views/Administrative/labels/view')
        }
      ]),
    ]
  }]