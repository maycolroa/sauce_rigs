import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'
import { middleware } from 'vue-router-middleware'

export default [{
    path: '/industrialsecure',
    component: LayoutModules,
    children: [
      {
        path: '',
        name: 'industrialsecure',
        component: Home,
      },
      ...middleware({ 'check-permission': 'activities_r' }, [
        {
          name: 'industrialsecure-activities',
          path: 'activities',
          component: () =>
            import('@/views/IndustrialSecure/activities/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'activities_c' }, [
        {
          name: 'industrialsecure-activities-create',
          path: 'activities/create',
          component: () =>
            import('@/views/IndustrialSecure/activities/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'activities_u' }, [
        {
          name: 'industrialsecure-activities-edit',
          path: 'activities/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/activities/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'activities_r' }, [
        {
          name: 'industrialsecure-activities-view',
          path: 'activities/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/activities/view')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_r' }, [ 
        {
          name: 'industrialsecure-dangers',
          path: 'dangers',
          component: () =>
            import('@/views/IndustrialSecure/dangers/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_c' }, [ 
        {
          name: 'industrialsecure-dangers-create',
          path: 'dangers/create',
          component: () =>
            import('@/views/IndustrialSecure/dangers/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_u' }, [ 
        {
          name: 'industrialsecure-dangers-edit',
          path: 'dangers/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangers/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_r' }, [ 
        {
          name: 'industrialsecure-dangers-view',
          path: 'dangers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangers/view')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [ 
        {
          name: 'industrialsecure-dangermatrix',
          path: 'dangermatrix',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-create',
          path: 'dangermatrix/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/create')
        },
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-addfields',
          path: 'dangermatrix/addfields',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/addfieldsRequest')
        },
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-clone',
          path: 'dangermatrix/clone',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/clone')
        },
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_u' }, [
        {
          name: 'industrialsecure-dangermatrix-edit',
          path: 'dangermatrix/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/edit')
        }
      ]), 
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [
        {
          name: 'industrialsecure-dangermatrix-view',
          path: 'dangermatrix/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/view')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_view_report' }, [
        {
          name: 'industrialsecure-dangermatrix-report',
          path: 'dangermatrix/report',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/report')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags',
          path: 'dangermatrix/tags',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-administrative-controls',
          path: 'dangermatrix/tags/administrativecontrols',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-engineering-controls',
          path: 'dangermatrix/tags/engineeringcontrols',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-possible-consequences-danger',
          path: 'dangermatrix/tagspossibleconsequencesdanger',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-substitutions',
          path: 'dangermatrix/tags/substitutions',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-warning-signage',
          path: 'dangermatrix/tags/warningsignage',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-epp',
          path: 'dangermatrix/tags/epp',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-participants',
          path: 'dangermatrix/tags/participants',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-danger-description',
          path: 'dangermatrix/tags/dangerdescription',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_view_report' }, [
        {
          name: 'industrialsecure-dangermatrix-report-history',
          path: 'dangermatrix/report/history',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/reportHistory')
        }
      ]),
      {
        name: 'industrialsecure-dangerousconditions',
        path: 'dangerousconditions',
        component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/index')
      },
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-incentives',
          path: 'dangerousconditions/incentives',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/incentives/create')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections',
          path: 'dangerousconditions/inspections',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/index')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'dangerousconditions-inspections-create',
          path: 'dangerousconditions/inspections/create',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/create')
        }
      ]),      
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'dangerousconditions-inspection-qualification-masive',
          path: 'dangerousconditions/inspections/qualification/masive',
          component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/inspections/qualificationMasive')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'dangerousconditions-inspections-import',
          path: 'dangerousconditions/inspections/import',
          component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/inspections/import')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections-request-firm',
          path: 'dangerousconditions/inspections/request/firm',
          component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/inspections/viewRequestFirm')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections-request-firm-view',
          path: 'dangerousconditions/inspections/request/firm/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/inspections/firmQualification')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections-view',
          path: 'dangerousconditions/inspections/view/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/view')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_u' }, [
        {
          name: 'dangerousconditions-inspections-edit',
          path: 'dangerousconditions/inspections/edit/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'dangerousconditions-inspections-clone',
          path: 'dangerousconditions/inspections/clone',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/clone')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections-qualification',
          path: 'dangerousconditions/inspections/qualification/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/indexQualification')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections-qualification-view',
          path: 'dangerousconditions/inspections/qualification/view/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/viewQualification')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_report_view' }, [
				{
					name: 'dangerousconditions-inspection-report',
					path: 'dangerousconditions/inspection/report',
					component: () =>
					import('@/views/IndustrialSecure/dangerousConditions/inspections/report')
				}
      ]),
      ...middleware({ 'check-permission': 'ph_reports_r' }, [
				{
          name: 'dangerousconditions-reports',
          path: 'reports',
          component: () => import('@/views/IndustrialSecure/dangerousConditions/reports/index')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_reports_c' }, [
        {
          name: 'dangerousconditions-reports-create',
          path: 'dangerousconditions/reports/create',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/reports/create')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_reports_r' }, [
        {
          name: 'dangerousconditions-reports-view',
          path: 'dangerousconditions/reports/view/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/reports/view')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_reports_u' }, [
        {
          name: 'dangerousconditions-reports-edit',
          path: 'dangerousconditions/reports/edit/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/reports/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_reports_qualifications' }, [
        {
          name: 'dangerousconditions-reports-qualifications',
          path: 'dangerousconditions/reports/qualifications/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/reports/qualifications')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_reports_informs_view' }, [
        {
          name: 'dangerousconditions-reports-informs',
          path: 'dangerousconditions/reports/informs',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/reports/informs')
        }
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_r' }, [ 
        {
          name: 'industrialsecure-riskmatrix',
          path: 'riskmatrix',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/index')
        }
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_c' }, [
        {
          name: 'industrialsecure-riskmatrix-create',
          path: 'riskmatrix/create',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/create')
        },
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_u' }, [
        {
          name: 'industrialsecure-riskmatrix-edit',
          path: 'riskmatrix/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/edit')
        }
      ]), 
      ...middleware({ 'check-permission': 'riskMatrix_r' }, [
        {
          name: 'industrialsecure-riskmatrix-view',
          path: 'riskmatrix/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/view')
        }
      ]),
      ...middleware({ 'check-permission': 'subProcesses_r' }, [
        {
          name: 'industrialsecure-subprocesses',
          path: 'subprocesses',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/subProcess/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'subProcesses_c' }, [
        {
          name: 'industrialsecure-subprocesses-create',
          path: 'subprocesses/create',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/subProcess/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'subProcesses_u' }, [
        {
          name: 'industrialsecure-subprocesses-edit',
          path: 'subprocesses/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/subProcess/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'subProcesses_r' }, [
        {
          name: 'industrialsecure-subprocesses-view',
          path: 'subprocesses/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/subProcess/view')
        }
      ]),
      ...middleware({ 'check-permission': 'risks_r' }, [
        {
          name: 'industrialsecure-risks',
          path: 'risks',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/risk/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'risks_c' }, [
        {
          name: 'industrialsecure-risks-create',
          path: 'risks/create',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/risk/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'risks_u' }, [
        {
          name: 'industrialsecure-risks-edit',
          path: 'risks/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/risk/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'risks_r' }, [
        {
          name: 'industrialsecure-risks-view',
          path: 'risks/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/risk/view')
        }
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_c' }, [
        {
          name: 'industrialsecure-riskmatrix-macroprocesses',
          path: 'macroprocess',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/macroprocess/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'riskMatrix_c' }, [
        {
          name: 'industrialsecure-riskmatrix-macroprocesses-view',
          path: 'macroprocess/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/macroprocess/view')
        }
      ]), 
      ...middleware({ 'check-permission': 'riskMatrix_c' }, [
        {
          name: 'industrialsecure-riskmatrix-macroprocesses-edit',
          path: 'macroprocess/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/macroprocess/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_c' }, [
        {
          name: 'industrialsecure-riskmatrix-report',
          path: 'riskmatrix/report',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/report')
        }
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_c' }, [
        {
          name: 'industrialsecure-riskmatrix-report-history',
          path: 'riskmatrix/report/history',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/reportHistory')
        }
      ]),
    ]
  }]