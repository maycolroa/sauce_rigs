import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/industrialhygiene',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'industrialhygiene',
      component: Home,
    }]
  }]