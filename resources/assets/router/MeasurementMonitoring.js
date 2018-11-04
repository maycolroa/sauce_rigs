import LayoutModules from '@/views/layoutModules'
import Home from '@/views/Home'

export default [{
    path: '/measurementmonitoring',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'measurementmonitoring',
      component: Home,
    }]
  }]