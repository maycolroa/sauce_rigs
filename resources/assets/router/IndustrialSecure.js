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
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [ 
        {
          name: 'industrialsecure-dangermatrix-export-masive',
          path: 'dangermatrix/exportMasive',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/exportMasive')
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
            import('@/views/IndustrialSecure/dangerMatrix/tagPossibleConsequencesDanger/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-possible-consequences-danger-create',
          path: 'dangermatrix/tags/tagspossibleconsequencesdanger/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagPossibleConsequencesDanger/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-possible-consequences-danger-edit',
          path: 'dangermatrix/tags/tagspossibleconsequencesdanger/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagPossibleConsequencesDanger/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-possible-consequences-danger-deleted',
          path: 'dangermatrix/tags/tagspossibleconsequencesdanger/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagPossibleConsequencesDanger/deleted')
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
            import('@/views/IndustrialSecure/dangerMatrix/tagParticipants/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-participants-create',
          path: 'dangermatrix/tags/participants/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagParticipants/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-participants-edit',
          path: 'dangermatrix/tags/participants/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagParticipants/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-participants-deleted',
          path: 'dangermatrix/tags/participants/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagParticipants/deleted')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-danger-description',
          path: 'dangermatrix/tags/dangerdescription',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagDangerDescription/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-danger-description-create',
          path: 'dangermatrix/tags/dangerdescription/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagDangerDescription/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-danger-description-edit',
          path: 'dangermatrix/tags/dangerdescription/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagDangerDescription/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-tags-danger-description-deleted',
          path: 'dangermatrix/tags/dangerdescription/delete/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tagDangerDescription/deleted')
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
      ...middleware({ 'check-permission': 'ph_inspections_report_view' }, [
				{
					name: 'dangerousconditions-inspection-report-menu',
					path: 'dangerousconditions/inspection/report/menu',
					component: () =>
					import('@/views/IndustrialSecure/dangerousConditions/inspections/indexReports')
				}
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_report_view' }, [
				{
					name: 'dangerousconditions-inspection-report-gestion',
					path: 'dangerousconditions/inspection/report/gestion',
					component: () =>
					import('@/views/IndustrialSecure/dangerousConditions/inspections/reportGestion')
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
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-index-causes',
          path: 'accidents/index/causes',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/indexCauses')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-categories',
          path: 'accidents/index/causes/categories',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/categories/index')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-categories-create',
          path: 'accidents/index/causes/categories/create',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/categories/create')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-categories-edit',
          path: 'accidents/index/causes/categories/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/categories/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-categories-view',
          path: 'accidents/index/causes/categories/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/categories/view')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_u' }, [
        {
          name: 'industrialsecure-accidentswork-investigation',
          path: 'accidents/investigation/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/investigation')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_u' }, [
        {
          name: 'industrialsecure-accidentswork-send-pdf',
          path: 'accidents/sendEmail/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/sendMailPdf')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-items',
          path: 'accidents/index/causes/items',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/items/index')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-items-create',
          path: 'accidents/index/causes/items/create',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/items/create')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-items-edit',
          path: 'accidents/index/causes/items/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/items/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'accidentsWork_c' }, [
        {
          name: 'industrialsecure-accidentswork-causes-items-view',
          path: 'accidents/index/causes/items/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/accidentsWorks/items/view')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'dangerousconditions-inspections-perzonalized-create',
          path: 'dangerousconditions/inspections/perzonalized/create',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/personalizadas/create')
        }
      ]),
			...middleware({ 'check-permission': 'ph_inspections_r' }, [
				{
          name: 'dangerousconditions-inspections-customHelpers',
          path: 'dangerousconditions/inspections/customHelpers',
          component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/helpers/index')
        }
			]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections-customHelpers-view',
          path: 'dangerousconditions/inspections/customHelpers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/helpers/view')
				}
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [
				{
          name: 'dangerMatrix-customHelpers',
          path: 'dangerMatrix/customHelpers',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/helpers/index')
        }
			]),
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [
        {
          name: 'dangerMatrix-customHelpers-view',
          path: 'dangerMatrix/customHelpers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/helpers/view')
				}
      ]),
      ...middleware({ 'check-permission': 'elements_r' }, [
				{
          name: 'epp-customHelpers',
          path: 'epp/customHelpers',
          component: () =>
            import('@/views/IndustrialSecure/epp/helpers/index')
        }
			]),
      ...middleware({ 'check-permission': 'elements_r' }, [
        {
          name: 'epp-customHelpers-view',
          path: 'epp/customHelpers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/epp/helpers/view')
				}
      ]),
      ...middleware({ 'check-permission': 'riskMatrix_r' }, [
				{
          name: 'riskMatrix-customHelpers',
          path: 'riskMatrix/customHelpers',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/helpers/index')
        }
			]),
      ...middleware({ 'check-permission': 'riskMatrix_r' }, [
        {
          name: 'riskMatrix-customHelpers-view',
          path: 'riskMatrix/customHelpers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/riskMatrix/helpers/view')
				}
      ]),
      ...middleware({ 'check-permission': 'roadsafety_documents_r' }, [ 
        {
          name: 'industrialsecure-roadsafety',
          path: 'roadsafety/menu',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/indexMenu')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_documents_r' }, [
        {
          name: 'industrialsecure-roadsafety-documents',
          path: 'roadsafety/documents',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/documents/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_documents_c' }, [
        {
          name: 'industrialsecure-roadsafety-documents-create',
          path: 'roadsafety/documents/create',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/documents/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_documents_u' }, [
        {
          name: 'industrialsecure-roadsafety-documents-edit',
          path: 'roadsafety/documents/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/documents/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_documents_r' }, [
        {
          name: 'industrialsecure-roadsafety-documents-view',
          path: 'roadsafety/documents/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/documents/view')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles',
          path: 'roadsafety/vehicles',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_vehicles_c' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-create',
          path: 'roadsafety/vehicles/create',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_vehicles_u' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-edit',
          path: 'roadsafety/vehicles/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-view',
          path: 'roadsafety/vehicles/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/view')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-maintenance',
          path: 'roadsafety/vehicles/maintenance/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/maintenance/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_vehicles_c' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-maintenance-create',
          path: 'roadsafety/vehicles/maintenance/create/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/maintenance/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_vehicles_u' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-maintenance-edit',
          path: 'roadsafety/vehicles/maintenance/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/maintenance/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-maintenance-view',
          path: 'roadsafety/vehicles/maintenance/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/maintenance/view')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
				{
          name: 'industrialsecure-roadsafety-customHelpers',
          path: 'roadsafety/customHelpers',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/helpers/index')
        }
			]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
        {
          name: 'industrialsecure-roadsafety-customHelpers-view',
          path: 'roadsafety/customHelpers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/helpers/view')
				}
      ]),
      ...middleware({ 'check-permission': 'roadsafety_drivers_r' }, [
        {
          name: 'industrialsecure-roadsafety-drivers',
          path: 'roadsafety/drivers',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_drivers_c' }, [
        {
          name: 'industrialsecure-roadsafety-drivers-create',
          path: 'roadsafety/drivers/create',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_drivers_u' }, [
        {
          name: 'industrialsecure-roadsafety-drivers-edit',
          path: 'roadsafety/drivers/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_drivers_r' }, [
        {
          name: 'industrialsecure-roadsafety-drivers-view',
          path: 'roadsafety/drivers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/view')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-combustible',
          path: 'roadsafety/vehicles/combustible/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/combustible/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_vehicles_c' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-combustible-create',
          path: 'roadsafety/vehicles/combustible/create/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/combustible/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_vehicles_u' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-combustible-edit',
          path: 'roadsafety/vehicles/combustible/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/combustible/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_vehicles_r' }, [
        {
          name: 'industrialsecure-roadsafety-vehicles-combustible-view',
          path: 'roadsafety/vehicles/combustible/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/vehicles/combustible/view')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_r' }, [
        {
          name: 'roadSafety-inspections-menu',
          path: 'roadSafety/inspectionsMenu',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/indexMenu')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_r' }, [
        {
          name: 'roadSafety-inspections',
          path: 'roadSafety/inspections',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/index')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_c' }, [
        {
          name: 'roadSafety-inspections-create',
          path: 'roadSafety/inspections/create',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/create')
        }
      ]),      
      ...middleware({ 'check-permission': 'roadsafety_inspections_c' }, [
        {
          name: 'roadSafety-inspection-qualification-masive',
          path: 'roadSafety/inspections/qualification/masive',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/inspections/qualifications/qualificationMasive')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_c' }, [
        {
          name: 'roadSafety-inspections-import',
          path: 'roadSafety/inspections/import',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/inspections/import')
        }
      ]),
      /*...middleware({ 'check-permission': 'roadsafety_inspections_r' }, [
        {
          name: 'roadSafety-inspections-request-firm',
          path: 'roadSafety/inspections/request/firm',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/inspections/viewRequestFirm')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_r' }, [
        {
          name: 'roadSafety-inspections-request-firm-view',
          path: 'roadSafety/inspections/request/firm/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/inspections/firmQualification')
        }
      ]),*/
      ...middleware({ 'check-permission': 'roadsafety_inspections_r' }, [
        {
          name: 'roadSafety-inspections-view',
          path: 'roadSafety/inspections/view/:id',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/view')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_u' }, [
        {
          name: 'roadSafety-inspections-edit',
          path: 'roadSafety/inspections/edit/:id',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_c' }, [
        {
          name: 'roadSafety-inspections-clone',
          path: 'roadSafety/inspections/clone',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/clone')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_c' }, [
        {
          name: 'roadSafety-inspections-perzonalized-create',
          path: 'roadSafety/inspections/perzonalized/create',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/personalizadas/create')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_r' }, [
        {
          name: 'roadSafety-inspections-qualification',
          path: 'roadSafety/inspections/qualification/:id',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/qualifications/indexQualification')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_r' }, [
        {
          name: 'roadSafety-inspections-qualification-view',
          path: 'roadSafety/inspections/qualification/view/:id',
          component: () =>
              import('@/views/IndustrialSecure/roadSafety/inspections/qualifications/viewQualification')
        }
      ]),
      /*...middleware({ 'check-permission': 'roadsafety_inspections_report_view' }, [
				{
					name: 'roadSafety-inspection-report',
					path: 'roadSafety/inspection/report',
					component: () =>
					import('@/views/IndustrialSecure/roadSafety/inspections/report')
				}
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_report_view' }, [
				{
					name: 'roadSafety-inspection-report-menu',
					path: 'roadSafety/inspection/report/menu',
					component: () =>
					import('@/views/IndustrialSecure/roadSafety/inspections/indexReports')
				}
      ]),
      ...middleware({ 'check-permission': 'roadsafety_inspections_report_view' }, [
				{
					name: 'roadSafety-inspection-report-gestion',
					path: 'roadSafety/inspection/report/gestion',
					component: () =>
					import('@/views/IndustrialSecure/roadSafety/inspections/reportGestion')
				}
      ]),*/
      ...middleware({ 'check-permission': 'roadsafety_drivers_c' }, [
        {
          name: 'industrialsecure-roadsafety-drivers-infraction',
          path: 'roadsafety/drivers/infraction/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/infraction/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_drivers_c' }, [
        {
          name: 'industrialsecure-roadsafety-drivers-infraction-create',
          path: 'roadsafety/drivers/infraction/create/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/infraction/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'roadsafety_drivers_u' }, [
        {
          name: 'industrialsecure-roadsafety-drivers-infraction-edit',
          path: 'roadsafety/drivers/infraction/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/infraction/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'roadsafety_drivers_r' }, [
        {
          name: 'industrialsecure-roadsafety-drivers-infraction-view',
          path: 'roadsafety/drivers/infraction/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/roadSafety/drivers/infraction/view')
        }
      ]),
			...middleware({ 'check-permission': 'roadsafety_trainings_r' }, [
				{
					name: 'industrialsecure-roadsafety-trainings',
					path: 'roadsafety/trainings',
					component: () =>
					import('@/views/IndustrialSecure/roadSafety/trainings/index')
				}
			]),
			...middleware({ 'check-permission': 'roadsafety_trainings_c' }, [
				{
					name: 'industrialsecure-roadsafety-trainings-create',
					path: 'roadsafety/trainings/create',
					component: () =>
					import('@/views/IndustrialSecure/roadSafety/trainings/create')
				}
			]),
			...middleware({ 'check-permission': 'roadsafety_trainings_u' }, [
				{
					name: 'industrialsecure-roadsafety-trainings-edit',
					path: 'roadsafety/trainings/edit/:id',
					component: () =>
					import('@/views/IndustrialSecure/roadSafety/trainings/edit')
				}
			]),
			...middleware({ 'check-permission': 'roadsafety_trainings_r' }, [
				{
					name: 'industrialsecure-roadsafety-trainings-view',
					path: 'roadsafety/trainings/view/:id',
					component: () =>
					import('@/views/IndustrialSecure/roadSafety/trainings/view')
				}
      ]),
      ...middleware({ 'check-permission': 'roadsafety_trainings_r' }, [
				{
				name: 'industrialsecure-roadsafety-trainings-employees',
				path: 'roadsafety/trainings/employee/:id',
				component: () =>
					import('@/views/IndustrialSecure/roadSafety/trainings/indexEmployee')
						}
			]),
			...middleware({ 'check-permission': 'roadsafety_trainings_r' }, [
				{
				name: 'industrialsecure-roadsafety-trainings-employee-view',
				path: 'roadsafety/trainings/employee/view/:id',
				component: () =>
					import('@/views/IndustrialSecure/roadSafety/trainings/viewEmployee')
						}
			]),
      ...middleware({ 'check-permission': 'dangerMatrix_methodology_r' }, [
        {
          name: 'industrialsecure-complementary-methodology',
          path: 'complementaryMethodology',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/complementaryMethodology/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'dangerMatrix_methodology_c' }, [
        {
          name: 'industrialsecure-complementary-methodology-create',
          path: 'complementaryMethodology/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/complementaryMethodology/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'dangerMatrix_methodology_u' }, [
        {
          name: 'industrialsecure-complementary-methodology-edit',
          path: 'complementaryMethodology/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/complementaryMethodology/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_methodology_r' }, [
        {
          name: 'industrialsecure-complementary-methodology-view',
          path: 'complementaryMethodology/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/complementaryMethodology/view')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_methodology_r' }, [
        {
          name: 'industrialsecure-complementary-methodology-history',
          path: 'complementaryMethodology/history/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/complementaryMethodology/indexHistory')
        }
      ]),
    ]
  }]