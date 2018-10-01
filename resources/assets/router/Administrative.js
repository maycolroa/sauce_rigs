import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/administrative',
    component: LayoutModules,
    //props: true,
    children: [{
      path: '',
      name: 'administrative',
      component: Home,
    }]
  }]