import Layout from '@/views/layout'

export default [{
    path: '/biologicalmonitoring/audiometry',
    component: Layout,
    children: [{   
        path: '',
        name: 'audiometry',
        component: () => import('@/views/biologicalmonitoring/audiometry/index')
      },{
          name: 'audiometry-create',
        path: 'create',
        component: () => import('@/views/biologicalmonitoring/audiometry/create')
       }]
  }]