import LayoutModules from '@/views/layoutModules'
import Home from '@/views/Home'

export default [{
    path: '/industrialhygiene',
    component: LayoutModules,
    children: [{
      path: '',
      name: 'industrialhygiene',
      component: Home,
    }]
  }]