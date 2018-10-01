import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
  path: '/preventiveoccupationalmedicine',
  component: LayoutModules,
  children: [{
    path: '',
    name: 'preventiveoccupationalmedicine',
    component: Home
  }, {
    path: 'biologicalmonitoring/audiometry',
    name: 'biologicalmonitoring-audiometry',
    component: () =>
        import('@/views/PreventiveOccupationalMedicine/biologicalmonitoring/audiometry/index')
  }]
}]

//preventiveoccupationalmedicine/biologicalmonitoring/audiometry