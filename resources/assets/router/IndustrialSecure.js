import LayoutModules from '@/views/layoutModules';
import Home from '@/views/IndustrialSecure/home';
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
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [ 
        {
          name: 'industrialsecure-dangermatrix-menu',
          path: 'dangermatrix-menu',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexMenu')
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
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [
        {
          name: 'industrialsecure-dangermatrix-report-detail',
          path: 'dangermatrix/reportDetail/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/viewReportDetail')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [
        {
          name: 'industrialsecure-dangermatrix-report-detail-history',
          path: 'dangermatrix/reportDetailHistory/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/viewReportDetailHistory')
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
          name: 'industrialsecure-dangermatrix-log-qualification',
          path: 'dangermatrix/logQualification',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexLogQualification')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-administrative-controls',
          path: 'dangermatrix/tags/administrativecontrols',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagAdministrativeControls/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-administrative-controls-create',
          path: 'dangermatrix/tags/administrativecontrols/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagAdministrativeControls/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-administrative-controls-edit',
          path: 'dangermatrix/tags/administrativecontrols/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagAdministrativeControls/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-administrative-controls-deleted',
          path: 'dangermatrix/tags/administrativecontrols/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagAdministrativeControls/deleted')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-engineering-controls',
          path: 'dangermatrix/tags/engineeringcontrols',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEngineeringControls/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-engineering-controls-create',
          path: 'dangermatrix/tags/engineeringcontrols/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEngineeringControls/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-engineering-controls-edit',
          path: 'dangermatrix/tags/engineeringcontrols/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEngineeringControls/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-engineering-controls-deleted',
          path: 'dangermatrix/tags/engineeringcontrols/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEngineeringControls/deleted')
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
            import('@/views/IndustrialSecure/dangerMatrix/tagSubstitutionControls/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-substitutions-create',
          path: 'dangermatrix/tags/substitutions/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagSubstitutionControls/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-substitutions-edit',
          path: 'dangermatrix/tags/substitutions/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagSubstitutionControls/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-substitutions-deleted',
          path: 'dangermatrix/tags/substitutions/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagSubstitutionControls/deleted')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-warning-signage',
          path: 'dangermatrix/tags/warningsignage',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagWarningSignageControls/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-warning-signage-create',
          path: 'dangermatrix/tags/warningsignage/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagWarningSignageControls/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-warning-signage-edit',
          path: 'dangermatrix/tags/warningsignage/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagWarningSignageControls/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-warning-signage-deleted',
          path: 'dangermatrix/tags/warningsignage/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagWarningSignageControls/deleted')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-epp',
          path: 'dangermatrix/tags/epp',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEppControls/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-epp-create',
          path: 'dangermatrix/tags/eppcontrols/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEppControls/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-epp-edit',
          path: 'dangermatrix/tags/eppcontrols/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEppControls/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-epp-deleted',
          path: 'dangermatrix/tags/eppcontrols/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagEppControls/deleted')
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
      ...middleware({ 'check-permission': 'riskMatrix_view_report' }, [
        {
          name: 'industrialsecure-riskmatrix-report',
          path: 'riskmatrix/report',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/report')
        }
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_view_report' }, [
        {
          name: 'industrialsecure-riskmatrix-report-history',
          path: 'riskmatrix/report/history',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/reportHistory')
        }
      ]), 
      ...middleware({ 'check-permission': 'riskMatrix_r' }, [ 
        {
          name: 'industrialsecure-riskmatrix-menu',
          path: 'riskmatrix-menu',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/indexMenu')
        }
      ]),
      ...middleware({ 'check-permission': 'elements_r' }, [ 
        {
          name: 'industrialsecure-epp',
          path: 'epp/menu',
          component: () =>
            import('@/views/IndustrialSecure/epp/indexMenu')
        }
      ]),
      ...middleware({ 'check-permission': 'elements_r' }, [
        {
          name: 'industrialsecure-epps-elements',
          path: 'epps/elements',
          component: () =>
            import('@/views/IndustrialSecure/epp/elements/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'elements_c' }, [
        {
          name: 'industrialsecure-epps-elements-create',
          path: 'epps/elements/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/elements/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'elements_u' }, [
        {
          name: 'industrialsecure-epps-elements-edit',
          path: 'epps/elements/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/elements/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'elements_r' }, [
        {
          name: 'industrialsecure-epps-elements-view',
          path: 'epps/elements/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/elements/view')
        }
      ]),
      ...middleware({ 'check-permission': 'elements_c' }, [
        {
          name: 'industrialsecure-epps-elements-import-balance-inicial',
          path: 'epps/elements/balanceInicial',
          component: () =>
            import('@/views/IndustrialSecure/epp/elements/importBalanceInicial')
        }
      ]), 
      ...middleware({ 'check-permission': 'location_r' }, [
        {
          name: 'industrialsecure-epps-locations',
          path: 'epps/locations',
          component: () =>
            import('@/views/IndustrialSecure/epp/locations/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'location_c' }, [
        {
          name: 'industrialsecure-epps-locations-create',
          path: 'epps/locations/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/locations/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'location_u' }, [
        {
          name: 'industrialsecure-epps-locations-edit',
          path: 'epps/locations/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/locations/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'location_r' }, [
        {
          name: 'industrialsecure-epps-locations-view',
          path: 'epps/locations/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/locations/view')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-menu',
          path: 'epps/transactions/menu',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/indexMenu')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions',
          path: 'epps/transactions',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/indexEmployee')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_c' }, [
        {
          name: 'industrialsecure-epps-transactions-create',
          path: 'epps/transactions/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_u' }, [
        {
          name: 'industrialsecure-epps-transactions-edit',
          path: 'epps/transactions/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-view',
          path: 'epps/transactions/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/view')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-returns',
          path: 'epps/transactions/returns',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/indexEmployeeReturns')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_c' }, [
        {
          name: 'industrialsecure-epps-transactions-returns-create',
          path: 'epps/transactions/returns/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/createReturns')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_u' }, [
        {
          name: 'industrialsecure-epps-transactions-returns-edit',
          path: 'epps/transactions/returns/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/editReturns')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-returns-view',
          path: 'epps/transactions/returns/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/viewReturns')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-income',
          path: 'epps/transactions/income',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/income/index') 
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_c' }, [
        {
          name: 'industrialsecure-epps-transactions-income-create',
          path: 'epps/transactions/income/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/income/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_u' }, [
        {
          name: 'industrialsecure-epps-transactions-income-edit',
          path: 'epps/transactions/income/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/income/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-income-view',
          path: 'epps/transactions/income/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/income/view')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-exit',
          path: 'epps/transactions/exit',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/exit/index') 
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_c' }, [
        {
          name: 'industrialsecure-epps-transactions-exit-create',
          path: 'epps/transactions/exit/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/exit/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_u' }, [
        {
          name: 'industrialsecure-epps-transactions-exit-edit',
          path: 'epps/transactions/exit/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/exit/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-exit-view',
          path: 'epps/transactions/exit/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/exit/view')
        }
      ]),
      ...middleware({ 'check-permission': 'configuration_epp_c' }, [
        {
          name: 'industrialsecure-epps-settings',
          path: 'epps/configurations',
          component: () =>
            import('@/views/IndustrialSecure/epp/settings/index')
        }
      ]),
      ...middleware({ 'check-permission': 'elements_r' }, [
        {
          name: 'industrialsecure-epps-reports',
          path: 'epps/report',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/reports')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-transfers-location',
          path: 'epps/transactions/transfers/location',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/transferLocation/index') 
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_c' }, [
        {
          name: 'industrialsecure-epps-transactions-transfers-location-create',
          path: 'epps/transactions/transfers/location/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/transferLocation/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_u' }, [
        {
          name: 'industrialsecure-epps-transactions-transfers-location-edit',
          path: 'epps/transactions/transfers/location/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/transferLocation/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-transfers-location-view',
          path: 'epps/transactions/transfers/location/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/transferLocation/view')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-reception',
          path: 'epps/transactions/reception',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/reception/index') 
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_c' }, [
        {
          name: 'industrialsecure-epps-transactions-reception-create',
          path: 'epps/transactions/reception/create',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/reception/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'transaction_u' }, [
        {
          name: 'industrialsecure-epps-transactions-reception-edit',
          path: 'epps/transactions/reception/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/reception/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'transaction_r' }, [
        {
          name: 'industrialsecure-epps-transactions-reception-view',
          path: 'epps/transactions/reception/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/transactions/reception/view')
        }
      ]),
			...middleware({ 'check-permission': ['activities_r', 'elements_r', 'location_r', 'subProcesses_r', 'dangerMatrix_r', 'ph_inspections_r', 'ph_reports_r'] }, [
				{
					name: 'industrialsecure-documentssecurity',
					path: 'documents',
					component: () => import('@/views/IndustrialSecure/documents/index')
				}
			]),
			...middleware({ 'check-permission': ['activities_r', 'elements_r', 'location_r', 'subProcesses_r', 'dangerMatrix_r', 'ph_inspections_r', 'ph_reports_r'] }, [
				{
					name: 'industrialsecure-documentssecurity-create',
					path: 'documents/create',
					component: () => import('@/views/IndustrialSecure/documents/create')
				}
			]),
			...middleware({ 'check-permission': ['activities_r', 'elements_r', 'location_r', 'subProcesses_r', 'dangerMatrix_r', 'ph_inspections_r', 'ph_reports_r'] }, [
				{
					name: 'industrialsecure-documentssecurity-edit',
					path: 'documents/edit/:id',
					component: () => import('@/views/IndustrialSecure/documents/edit')
				}
			]),
			...middleware({ 'check-permission': ['activities_r', 'elements_r', 'location_r', 'subProcesses_r', 'dangerMatrix_r', 'ph_inspections_r', 'ph_reports_r'] }, [
				{
					name: 'industrialsecure-documentssecurity-view',
					path: 'documents/view/:id',
					component: () => import('@/views/IndustrialSecure/documents/view')
				}
			]),
      ...middleware({ 'check-permission': 'accidentsWork_r' }, [
        {
          name: 'industrialsecure-accidentswork',
          path: 'accidents',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/index') 
        }
      ]), 
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-create',
          path: 'accidents/create',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'accidentsWork_u' }, [
        {
          name: 'industrialsecure-accidentswork-edit',
          path: 'accidents/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_r' }, [
        {
          name: 'industrialsecure-accidentswork-view',
          path: 'accidents/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/view')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'industrialsecure-configurations',
          path: 'configurations',
          component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/configurations/index')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_r' }, [
        {
          name: 'industrialsecure-accidentswork-report',
          path: 'accidents/report',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/report')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_u' }, [
        {
          name: 'industrialsecure-accidentswork-causes',
          path: 'accidents/causes/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/causes')
        }
      ]),
    ]
  }]