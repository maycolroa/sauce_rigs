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
          name: 'system-licenses-reasignacion-index',
          path: 'licenses/reasignation',
          component: () =>
            import('@/views/System/licenses/indexReasignacion')
        }
      ]),  
      ...middleware({ 'check-permission': 'licenses_c' }, [
        {
          name: 'system-licenses-reasignar',
          path: 'licenses/reasignation/edit/:id',
          component: () =>
            import('@/views/System/licenses/editReasignar')
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
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-licenses-report',
          path: 'licenses/report',
          component: () =>
            import('@/views/System/licenses/report')
        }
      ]),
      ...middleware({ 'check-permission': 'licenses_c' }, [
        {
          name: 'system-licenses-configuration',
          path: 'licenses/configurations',
          component: () =>
            import('@/views/System/licenses/configuration')
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
      ...middleware({ 'check-permission': 'companies_u' }, [
        {
          name: 'system-companies-edit-info',
          path: 'companies/edit/info/:id',
          component: () =>
            import('@/views/System/companies/editInfo')
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
          name: 'system-customermonitoring-riskMatrix',
          path: 'customermonitoring/riskMatrix',
          component: () =>
            import('@/views/System/customerMonitoring/indexRiskMatrix')
        }
      ]), 
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-epp',
          path: 'customermonitoring/epp',
          component: () =>
            import('@/views/System/customerMonitoring/indexEpp')
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
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-automaticsSend',
          path: 'customermonitoring/automaticsSend',
          component: () =>
            import('@/views/System/customerMonitoring/sendAutomatics/index')
        }
      ]),
      ...middleware({ 'check-permission': 'customerMonitoring_r' }, [
        {
          name: 'system-customermonitoring-automaticsSend-edit',
          path: 'customermonitoring/automaticsSend/:id',
          component: () =>
            import('@/views/System/customerMonitoring/sendAutomatics/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'companies_r' }, [
        {
          name: 'system-companygroup',
          path: 'companygroup',
          component: () =>
            import('@/views/System/companyGroup/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'companies_c' }, [
        {
          name: 'system-companygroup-create',
          path: 'companygroup/create',
          component: () =>
            import('@/views/System/companyGroup/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'companies_u' }, [
        {
          name: 'system-companygroup-edit',
          path: 'companygroup/edit/:id',
          component: () =>
            import('@/views/System/companyGroup/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'companies_r' }, [
        {
          name: 'system-companygroup-view',
          path: 'companygroup/view/:id',
          component: () =>
            import('@/views/System/companyGroup/view')
        }
      ]),
      ...middleware({ 'check-permission': 'newsletterSend_r' }, [
        {
          name: 'system-newslettersend',
          path: 'newslettersend',
          component: () =>
            import('@/views/System/newsletterSend/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'newsletterSend_c' }, [
        {
          name: 'system-newslettersend-create',
          path: 'newslettersend/create',
          component: () =>
            import('@/views/System/newsletterSend/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'newsletterSend_u' }, [
        {
          name: 'system-newslettersend-edit',
          path: 'newslettersend/edit/:id',
          component: () =>
            import('@/views/System/newsletterSend/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'newsletterSend_r' }, [
        {
          name: 'system-newslettersend-view',
          path: 'newslettersend/view/:id',
          component: () =>
            import('@/views/System/newsletterSend/view')
        }
      ]),
      ...middleware({ 'check-permission': 'newsletterSend_r' }, [
        {
          name: 'system-newslettersend-program',
          path: 'newslettersend/program/:id',
          component: () =>
            import('@/views/System/newsletterSend/switchStatus')
        }
      ]),
      ...middleware({ 'check-permission': 'newsletterSend_r' }, [
        {
          name: 'system-newslettersend-opens',
          path: 'newslettersend/opens/:id',
          component: () =>
            import('@/views/System/newsletterSend/reportsEmailOpen')
        }
      ]),
      ...middleware({ 'check-permission': 'helpers_r' }, [
        {
          name: 'system-helpers',
          path: 'helpers',
          component: () =>
            import('@/views/System/helpers/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'helpers_c' }, [
        {
          name: 'system-helpers-create',
          path: 'helpers/create',
          component: () =>
            import('@/views/System/helpers/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'helpers_u' }, [
        {
          name: 'system-helpers-edit',
          path: 'helpers/edit/:id',
          component: () =>
            import('@/views/System/helpers/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'helpers_r' }, [
        {
          name: 'system-helpers-view',
          path: 'helpers/view/:id',
          component: () =>
            import('@/views/System/helpers/view')
        }
      ]),
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-eps',
          path: 'eps',
          component: () =>
            import('@/views/System/eps/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_c' }, [
        {
          name: 'system-eps-create',
          path: 'eps/create',
          component: () =>
            import('@/views/System/eps/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_u' }, [
        {
          name: 'system-eps-edit',
          path: 'eps/edit/:id',
          component: () =>
            import('@/views/System/eps/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-eps-view',
          path: 'eps/view/:id',
          component: () =>
            import('@/views/System/eps/view')
        }
      ]),
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-arl',
          path: 'arl',
          component: () =>
            import('@/views/System/arl/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_c' }, [
        {
          name: 'system-arl-create',
          path: 'arl/create',
          component: () =>
            import('@/views/System/arl/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_u' }, [
        {
          name: 'system-arl-edit',
          path: 'arl/edit/:id',
          component: () =>
            import('@/views/System/arl/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-arl-view',
          path: 'arl/view/:id',
          component: () =>
            import('@/views/System/arl/view')
        }
      ]),
    ]
  }]