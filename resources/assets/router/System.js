import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'
import { middleware } from 'vue-router-middleware'

export default [{
    path: '/system',
    component: LayoutModules,
    children: [
      {
        path: '',
        name: 'system',
        component: Home,
      },
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-licenses',
          path: 'licenses',
          component: () =>
            import('@/views/System/licenses/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_c' }, [
        {
          name: 'system-licenses-create',
          path: 'licenses/create',
          component: () =>
            import('@/views/System/licenses/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_u' }, [
        {
          name: 'system-licenses-edit',
          path: 'licenses/edit/:id',
          component: () =>
            import('@/views/System/licenses/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-licenses-view',
          path: 'licenses/view/:id',
          component: () =>
            import('@/views/System/licenses/view')
        }
      ]),
      ...middleware({ 'check-permission': 'logMails_r' }, [
        {
          name: 'system-logmails',
          path: 'logmails',
          component: () =>
            import('@/views/System/logMails/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'logMails_r' }, [
        {
          name: 'system-logmails-view',
          path: 'logmails/view/:id',
          component: () =>
            import('@/views/System/logMails/view')
        }
      ]),
      ...middleware({ 'check-permission': 'labels_r' }, [
        {
          name: 'system-labels',
          path: 'labels',
          component: () =>
            import('@/views/System/labels/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'labels_u' }, [
        {
          name: 'system-labels-edit',
          path: 'labels/edit/:id',
          component: () =>
            import('@/views/System/labels/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'labels_r' }, [
        {
          name: 'system-labels-view',
          path: 'labels/view/:id',
          component: () =>
            import('@/views/System/labels/view')
        }
      ]),
      ...middleware({ 'check-permission': 'companies_r' }, [
        {
          name: 'system-companies',
          path: 'companies',
          component: () =>
            import('@/views/System/companies/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'companies_c' }, [
        {
          name: 'system-companies-create',
          path: 'companies/create',
          component: () =>
            import('@/views/System/companies/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'companies_u' }, [
        {
          name: 'system-companies-edit',
          path: 'companies/edit/:id',
          component: () =>
            import('@/views/System/companies/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'companies_r' }, [
        {
          name: 'system-companies-view',
          path: 'companies/view/:id',
          component: () =>
            import('@/views/System/companies/view')
        }
      ]),
      ...middleware({ 'check-permission': 'usersCompanies_r' }, [
        {
          name: 'system-userscompanies',
          path: 'userscompanies',
          component: () =>
            import('@/views/System/userCompanies/index')
        }
      ]),
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring',
          path: 'customermonitoring',
          component: () =>
            import('@/views/System/customerMonitoring/index')
        }
      ]),
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-dangerousconditions',
          path: 'customermonitoring/dangerousconditions',
          component: () =>
            import('@/views/System/customerMonitoring/indexDangerousConditions')
        }
      ]),
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-reinstatements',
          path: 'customermonitoring/reinstatements',
          component: () =>
            import('@/views/System/customerMonitoring/indexReinstatement')
        }
      ]), 
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-absenteeism',
          path: 'customermonitoring/absenteeism',
          component: () =>
            import('@/views/System/customerMonitoring/indexAbsenteeism')
        }
      ]), 
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-dangerMatrix',
          path: 'customermonitoring/dangerMatrix',
          component: () =>
            import('@/views/System/customerMonitoring/indexDangerMatrix')
        }
      ]), 
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-contract',
          path: 'customermonitoring/contract',
          component: () =>
            import('@/views/System/customerMonitoring/indexContract')
        }
      ]),
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-legalMatrix',
          path: 'customermonitoring/legalMatrix',
          component: () =>
            import('@/views/System/customerMonitoring/indexLegalMatrix')
        }
      ]),
    ]
  }]