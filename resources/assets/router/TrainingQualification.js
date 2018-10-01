import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/trainingqualification',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'trainingqualification',
      component: Home,
    }]
  }]