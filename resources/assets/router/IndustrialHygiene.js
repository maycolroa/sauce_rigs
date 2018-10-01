import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/industrialhygiene',
    component: LayoutModules,
    //props: true,
    children: [{
      path: '',
      name: 'industrialhygiene',
      component: Home,
    }]
  }]