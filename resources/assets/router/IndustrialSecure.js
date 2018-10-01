import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/industrialsecure',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'industrialsecure',
      component: Home,
    }]
  }]