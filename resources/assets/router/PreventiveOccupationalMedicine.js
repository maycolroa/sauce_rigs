import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'
import { middleware } from 'vue-router-middleware'

export default [{
  path: '/preventiveoccupationalmedicine',
  component: LayoutModules,
  children: [
    {
      name: 'preventiveoccupationalmedicine',
      path: '',
      component: Home
    },
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
      {
        name: 'biologicalmonitoring-audiometry',
        path: 'biologicalmonitoring/audiometry',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/indexMenu')
      }
    ]),
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
      {
        name: 'biologicalmonitoring-audiometry-index',
        path: 'biologicalmonitoring/audiometry/index',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/index')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_c' }, [
      {
        name: 'biologicalmonitoring-audiometry-create',
        path: 'biologicalmonitoring/audiometry/create',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/create')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_u' }, [
      {
        name: 'biologicalmonitoring-audiometry-edit',
        path: 'biologicalmonitoring/audiometry/edit/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/edit')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
      {
        name: 'biologicalmonitoring-audiometry-view',
        path: 'biologicalmonitoring/audiometry/view/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/view')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
      {
        name: 'biologicalmonitoring-audiometry-report',
        path: 'biologicalmonitoring/audiometry/report/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/report')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_informs_r' }, [
      {
        name: 'biologicalmonitoring-audiometry-informs',
        path: 'biologicalmonitoring/audiometry/informs',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/informs')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_inform_individual_r' }, [
      {
        name: 'biologicalmonitoring-audiometry-informs-individual',
        path: 'biologicalmonitoring/audiometry/informs/individual',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/informIndividual')
      }
    ]),
    {
      name: 'preventiveoccupationalmedicine-reinstatements',
      path: 'reinstatements',
      component: () => import('@/views/PreventiveOccupationalMedicine/reinstatements/index')
    },
    ...middleware({ 'check-permission': 'reinc_restrictions_r' }, [
      {
        name: 'reinstatements-restrictions',
        path: 'reinstatements/restrictions',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/reinstatements/restrictions/index')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_restrictions_c' }, [
      {
        name: 'reinstatements-restrictions-create',
        path: 'reinstatements/restrictions/create',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/restrictions/create')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_restrictions_u' }, [
      {
        name: 'reinstatements-restrictions-edit',
        path: 'reinstatements/restrictions/edit/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/restrictions/edit')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_restrictions_r' }, [
      {
        name: 'reinstatements-restrictions-view',
        path: 'reinstatements/restrictions/view/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/restrictions/view')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_checks_r' }, [
      {
        name: 'reinstatements-configuration',
        path: 'reinstatements/configurations',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/configuration/index')
      }
    ]),
    ...middleware({ 'check-permission': 'reinc_checks_r' }, [
      {
        name: 'reinstatements-checks',
        path: 'reinstatements/checks',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/index')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_checks_c' }, [
      {
        name: 'reinstatements-checks-create',
        path: 'reinstatements/checks/create',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/create')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_checks_u' }, [
      {
        name: 'reinstatements-checks-edit',
        path: 'reinstatements/checks/edit/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/edit')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_checks_r' }, [
      {
        name: 'reinstatements-checks-view',
        path: 'reinstatements/checks/view/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/view')
      }
    ]), 
    ...middleware({ 'check-permission': 'reinc_checks_c' }, [
      {
        name: 'reinstatements-checks-switchStatus',
        path: 'reinstatements/checks/switchStatus/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/switchStatus')
      }
    ]),
    ...middleware({ 'check-permission': 'reinc_checks_r' }, [
      {
        name: 'reinstatements-checks-letter',
        path: 'reinstatements/checks/letter/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/letter')
      }
    ]),
    ...middleware({ 'check-permission': 'reinc_checks_informs' }, [
      {
        name: 'reinstatements-informs',
        path: 'reinstatements/checks/informs',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/informs')
      }
    ]),
    ...middleware({ 'check-permission': 'reinc_checks_r' }, [
      {
        name: 'reinstatements-checks-letter-regenerate',
        path: 'reinstatements/checks/letterregenerate',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/checks/indexLetterRegenerate')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_musculoskeletalAnalysis_r' }, [
      {
        name: 'biologicalmonitoring-musculoskeletalanalysis',
        path: 'biologicalmonitoring/musculoskeletalanalysis',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/musculoskeletalAnalysis/index')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_musculoskeletalAnalysis_r' }, [
      {
        name: 'biologicalmonitoring-musculoskeletalanalysis-informs',
        path: 'biologicalmonitoring/musculoskeletalanalysis/informs',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/musculoskeletalAnalysis/informs')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_musculoskeletalAnalysis_r' }, [
      {
        name: 'biologicalmonitoring-musculoskeletalanalysis-reportindividual',
        path: 'biologicalmonitoring/musculoskeletalanalysis/reportindividual',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/musculoskeletalAnalysis/reportIndividual')
      }
    ]),
    ...middleware({ 'check-permission': 'biologicalMonitoring_respiratoryAnalysis_r' }, [
      {
        name: 'biologicalmonitoring-respiratoryanalysis',
        path: 'biologicalmonitoring/respiratoryanalysis',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/respiratoryAnalysis/index')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_respiratoryAnalysis_r' }, [
      {
        name: 'biologicalmonitoring-respiratoryanalysis-reportindividual',
        path: 'biologicalmonitoring/respiratoryanalysis/reportindividual',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/respiratoryAnalysis/reportIndividual')
      }
    ]),
    ...middleware({ 'check-permission': 'biologicalMonitoring_respiratoryAnalysis_r' }, [
      {
        name: 'biologicalmonitoring-respiratoryanalysis-informs',
        path: 'biologicalmonitoring/respiratoryanalysis/informs',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/respiratoryAnalysis/informs')
      }
    ]),
    {
      name: 'preventiveoccupationalmedicine-absenteeism',
      path: 'absenteeism',
      component: () => import('@/views/PreventiveOccupationalMedicine/absenteeism/index')
    },
    ...middleware({ 'check-permission': 'absen_reports_r' }, [
      {
        name: 'absenteeism-reports',
        path: 'absenteeism/reports',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/absenteeism/reports/index')
      }
    ]), 
    ...middleware({ 'check-permission': 'absen_reports_c' }, [
      {
        name: 'absenteeism-reports-create',
        path: 'absenteeism/reports/create',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/reports/create')
      }
    ]), 
    ...middleware({ 'check-permission': 'absen_reports_r' }, [
      {
        name: 'absenteeism-reports-view',
        path: 'absenteeism/reports/view/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/reports/view')
      }
    ]), 
    ...middleware({ 'check-permission': 'absen_reports_u' }, [
      {
        name: 'absenteeism-reports-edit',
        path: 'absenteeism/reports/edit/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/reports/edit')
      }
    ]),
    ...middleware({ 'check-permission': 'absen_reports_admin_user' }, [
      {
        name: 'absenteeism-reports-user-add',
        path: 'absenteeism/reports/user-add/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/reports/edit')
      }
    ]),
    ...middleware({ 'check-permission': 'absen_uploadFiles_r' }, [
      {
        name: 'absenteeism-upload-files',
        path: 'absenteeism/upload-files',
					component: () => import('@/views/PreventiveOccupationalMedicine/absenteeism/uploadFiles/index')
      }
    ]),
    ...middleware({ 'check-permission': 'absen_uploadFiles_r' }, [
      {
        name: 'absenteeism-upload-files-create',
        path: 'absenteeism/upload-files/create',
					component: () => import('@/views/PreventiveOccupationalMedicine/absenteeism/uploadFiles/create')
      }
    ]),
    ...middleware({ 'check-permission': 'absen_uploadFiles_error' }, [
      {
        name: 'absenteeism-upload-files-view-error',
        path: 'absenteeism/upload-files/view-error/:id',
					component: () => import('@/views/PreventiveOccupationalMedicine/absenteeism/uploadFiles/viewError')
      }
    ]),
    ...middleware({ 'check-permission': 'absen_uploadTalend_r' }, [
      {
        name: 'absenteeism-talends',
        path: 'absenteeism/talends',
        component: () =>
            import('@/views/PreventiveOccupationalMedicine/absenteeism/talends/index')
      }
    ]),
    ...middleware({ 'check-permission': 'absen_uploadTalend_c' }, [
      {
        name: 'absenteeism-talends-create',
        path: 'absenteeism/talends/create',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/talends/create')
      }
    ]), 
    ...middleware({ 'check-permission': 'absen_uploadTalend_u' }, [
      {
        name: 'absenteeism-talends-edit',
        path: 'absenteeism/talends/edit/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/talends/edit')
      }
    ]), 
    ...middleware({ 'check-permission': 'absen_uploadTalend_r' }, [
      {
        name: 'absenteeism-talends-view',
        path: 'absenteeism/talends/view/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/talends/view')
      }
    ]),
    ...middleware({ 'check-permission': 'reinc_disease_origin_r' }, [
        {
          name: 'reinstatements-disease-origin',
          path: 'reinstatements/diseaseOrigin',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/diseaseOrigin/index')
        }, 
      ]),
      ...middleware({ 'check-permission': 'reinc_disease_origin_c' }, [
        {
          name: 'reinstatements-disease-origin-create',
          path: 'reinstatements/diseaseOrigin/create',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/diseaseOrigin/create')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_disease_origin_u' }, [
        {
          name: 'reinstatements-disease-origin-edit',
          path: 'reinstatements/diseaseOrigin/edit/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/diseaseOrigin/edit')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_disease_origin_r' }, [
        {
          name: 'reinstatements-disease-origin-view',
          path: 'reinstatements/diseaseOrigin/view/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/diseaseOrigin/view')
        }
      ]),,
    ...middleware({ 'check-permission': 'reinc_origin_advisor_r' }, [
        {
          name: 'reinstatements-origin-advisor',
          path: 'reinstatements/originAdvisor',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/originAdvisor/index')
        }, 
      ]),
      ...middleware({ 'check-permission': 'reinc_origin_advisor_c' }, [
        {
          name: 'reinstatements-origin-advisor-create',
          path: 'reinstatements/originAdvisor/create',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/originAdvisor/create')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_origin_advisor_u' }, [
        {
          name: 'reinstatements-origin-advisor-edit',
          path: 'reinstatements/originAdvisor/edit/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/originAdvisor/edit')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_origin_advisor_r' }, [
        {
          name: 'reinstatements-origin-advisor-view',
          path: 'reinstatements/originAdvisor/view/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/originAdvisor/view')
        }
      ]),
      ...middleware({ 'check-permission': 'reinc_labor_conclusion_r' }, [
        {
          name: 'reinstatements-labor-conclusions',
          path: 'reinstatements/laborConclusions',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/laborConclusions/index')
        }, 
      ]),
      ...middleware({ 'check-permission': 'reinc_labor_conclusion_c' }, [
        {
          name: 'reinstatements-labor-conclusions-create',
          path: 'reinstatements/laborConclusions/create',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/laborConclusions/create')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_labor_conclusion_u' }, [
        {
          name: 'reinstatements-labor-conclusions-edit',
          path: 'reinstatements/laborConclusions/edit/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/laborConclusions/edit')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_labor_conclusion_r' }, [
        {
          name: 'reinstatements-labor-conclusions-view',
          path: 'reinstatements/laborConclusions/view/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/laborConclusions/view')
        }
      ]),
      ...middleware({ 'check-permission': 'reinc_medical_conclusion_r' }, [
        {
          name: 'reinstatements-medical-conclusions',
          path: 'reinstatements/medicalConclusions',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/medicalConclusions/index')
        }, 
      ]),
      ...middleware({ 'check-permission': 'reinc_medical_conclusion_c' }, [
        {
          name: 'reinstatements-medical-conclusions-create',
          path: 'reinstatements/medicalConclusions/create',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/medicalConclusions/create')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_medical_conclusion_u' }, [
        {
          name: 'reinstatements-medical-conclusions-edit',
          path: 'reinstatements/medicalConclusions/edit/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/medicalConclusions/edit')
        },
      ]),
      ...middleware({ 'check-permission': 'reinc_medical_conclusion_r' }, [
        {
          name: 'reinstatements-medical-conclusions-view',
          path: 'reinstatements/medicalConclusions/view/:id',
          component: () =>
          import('@/views/PreventiveOccupationalMedicine/reinstatements/medicalConclusions/view')
        }
      ]),
      ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations',
					path: 'evaluations',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/index')
				}
			]),
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-create',
					path: 'evaluations/create',
					component: () => import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/create')
				}
			]),
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-clone',
					path: 'evaluations/clone',
					component: () => import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/clone')
				}
			]),
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-edit',
					path: 'evaluations/edit/:id',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/edit')
				},
			]),
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-view',
					path: 'evaluations/view/:id',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/view')
				}
			]),
      ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-evaluate',
					path: 'evaluations/evaluate/:id',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/evaluationCreate')
				}
			]),
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-perform-clone',
					path: 'evaluations/contracts/clone',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/evaluationClone')
				}
			]),
		/*	...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-lessee',
					path: 'evaluations/perform',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/evaluationIndex')
				},
			]),*/
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-perform',
					path: 'evaluations/perform/:id',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/evaluationIndex')
				}
			]),
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-perform-view',
					path: 'evaluations/perform/view/:id',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/evaluationView')
				}
			]),
			...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
				{
					name: 'audiometry-evaluations-perform-edit',
					path: 'evaluations/perform/edit/:id',
					component: () =>
					import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/evaluations/evaluationEdit')
				}
			]),
      ...middleware({ 'check-permission': 'absen_config_r' }, [
        {
          name: 'absenteeism-configuration',
          path: 'absenteeism/configurations',
          component: () =>
            import('@/views/PreventiveOccupationalMedicine/absenteeism/configuration/index')
        }
      ]),
      ...middleware({ 'check-permission': ['biologicalMonitoring_audiometry_r', 'reinc_restrictions_r', 'reinc_checks_r', 'absen_uploadFiles_r', 'biologicalMonitoring_respiratoryAnalysis_r', 'biologicalMonitoring_musculoskeletalAnalysis_r', 'absen_reports_r'] }, [
				{
					name: 'preventiveoccupationalmedicine-documentspreventive',
					path: 'documentspreventive',
					component: () => import('@/views/PreventiveOccupationalMedicine/documents/index')
				}
			]),
			...middleware({ 'check-permission': ['biologicalMonitoring_audiometry_r', 'reinc_restrictions_r', 'reinc_checks_r', 'absen_uploadFiles_r', 'biologicalMonitoring_respiratoryAnalysis_r', 'biologicalMonitoring_musculoskeletalAnalysis_r', 'absen_reports_r'] }, [
				{
					name: 'preventiveoccupationalmedicine-documentspreventive-create',
					path: 'documentspreventive/create',
					component: () => import('@/views/PreventiveOccupationalMedicine/documents/create')
				}
			]),
			...middleware({ 'check-permission': ['biologicalMonitoring_audiometry_r', 'reinc_restrictions_r', 'reinc_checks_r', 'absen_uploadFiles_r', 'biologicalMonitoring_respiratoryAnalysis_r', 'biologicalMonitoring_musculoskeletalAnalysis_r', 'absen_reports_r'] }, [
				{
					name: 'preventiveoccupationalmedicine-documentspreventive-edit',
					path: 'documentspreventive/edit/:id',
					component: () => import('@/views/PreventiveOccupationalMedicine/documents/edit')
				}
			]),
			...middleware({ 'check-permission': ['biologicalMonitoring_audiometry_r', 'reinc_restrictions_r', 'reinc_checks_r', 'absen_uploadFiles_r', 'biologicalMonitoring_respiratoryAnalysis_r', 'biologicalMonitoring_musculoskeletalAnalysis_r', 'absen_reports_r'] }, [
				{
					name: 'preventiveoccupationalmedicine-documentspreventive-view',
					path: 'documentspreventive/view/:id',
					component: () => import('@/views/PreventiveOccupationalMedicine/documents/view')
				}
			]),
  ]
}]