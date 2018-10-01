import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/administrative',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'administrative',
      component: Home,
    }]
  }]