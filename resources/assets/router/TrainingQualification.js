import LayoutModules from '@/views/layoutModules'
import Home from '@/views/Home'

export default [{
    path: '/trainingqualification',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'trainingqualification',
      component: Home,
    }]
  }]