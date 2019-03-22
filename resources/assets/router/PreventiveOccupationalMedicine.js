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
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
      {
        name: 'biologicalmonitoring-audiometry-informs',
        path: 'biologicalmonitoring/audiometry/informs',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/informs')
      }
    ]), 
    ...middleware({ 'check-permission': 'biologicalMonitoring_audiometry_r' }, [
      {
        name: 'biologicalmonitoring-audiometry-informs-individual',
        path: 'biologicalmonitoring/audiometry/informs/individual',
        component: () =>
          import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/informIndividual')
      }
    ])
  ]
}]