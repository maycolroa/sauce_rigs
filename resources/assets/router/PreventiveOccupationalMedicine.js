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
    ...middleware({ 'check-permission': 'absen_uploadFiles_c' }, [
      {
        name: 'absenteeism-upload-files-create',
        path: 'absenteeism/upload-files/create',
					component: () => import('@/views/PreventiveOccupationalMedicine/absenteeism/uploadFiles/create')
      }
    ]), 
    ...middleware({ 'check-permission': 'absen_uploadFiles_r' }, [
      {
        name: 'absenteeism-upload-files-view',
        path: 'absenteeism/upload-files/view/:id',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/absenteeism/uploadFiles/view')
      }
    ]),
    ...middleware({ 'check-permission': 'absen_uploadTalend_c' }, [
      {
        name: 'absenteeism-upload-files-talend',
        path: 'absenteeism/upload-files/talend',
					component: () => import('@/views/PreventiveOccupationalMedicine/absenteeism/uploadFiles/talend')
      }
    ])
  ]
}]