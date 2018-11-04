import LayoutModules from '@/views/layoutModules'
import Home from '@/views/Home'

export default [{
  path: '/preventiveoccupationalmedicine',
  component: LayoutModules,
  children: [{
    name: 'preventiveoccupationalmedicine',
    path: '',
    component: Home
  }, {
    name: 'biologicalmonitoring-audiometry',
    path: 'biologicalmonitoring/audiometry',
    component: () =>
        import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/index')
  }, {
    name: 'biologicalmonitoring-audiometry-create',
    path: 'biologicalmonitoring/audiometry/create',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/create')
  }, {
    name: 'biologicalmonitoring-audiometry-edit',
    path: 'biologicalmonitoring/audiometry/edit/:id',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/edit')
  }, {
    name: 'biologicalmonitoring-audiometry-view',
    path: 'biologicalmonitoring/audiometry/view/:id',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/view')
  }, {
    name: 'biologicalmonitoring-audiometry-report',
    path: 'biologicalmonitoring/audiometry/report/:id',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/report')
  }]
}]