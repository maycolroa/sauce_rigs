import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/legalaspects',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'legalaspects',
      component: Home,
    }]
  }]