import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'

export default [{
    path: '/measurementmonitoring',
    component: LayoutModules,
    //props: true,
    children: [{
      path: '',
      name: 'measurementmonitoring',
      component: Home,
    }]
  }]