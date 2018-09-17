import Layout from '@/views/layout'

export default [{
  path: '/biologicalmonitoring/audiometry',
  component: Layout,
  children: [{
    path: '',
    name: 'biologicalmonitoring-audiometry',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/index')
  }, {
    name: 'biologicalmonitoring-audiometry-create',
    path: 'create',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/create')
  }, {
    name: 'biologicalmonitoring-audiometry-edit',
    path: 'edit/:id',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/edit')
  }, {
    name: 'biologicalmonitoring-audiometry-view',
    path: 'view/:id',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/view')
  }, {
    name: 'biologicalmonitoring-audiometry-report',
    path: 'report/:id',
    component: () =>
      import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/report')
  }]
}]