import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'
import { middleware } from 'vue-router-middleware'

export default [{
    path: '/system',
    component: LayoutModules,
    children: [
      {
        path: '',
        name: 'system',
        component: Home,
      },
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-licenses',
          path: 'licenses',
          component: () =>
            import('@/views/System/licenses/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_c' }, [
        {
          name: 'system-licenses-create',
          path: 'licenses/create',
          component: () =>
            import('@/views/System/licenses/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'licenses_u' }, [
        {
          name: 'system-licenses-edit',
          path: 'licenses/edit/:id',
          component: () =>
            import('@/views/System/licenses/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'licenses_r' }, [
        {
          name: 'system-licenses-view',
          path: 'licenses/view/:id',
          component: () =>
            import('@/views/System/licenses/view')
        }
      ]),
      ...middleware({ 'check-permission': 'logMails_r' }, [
        {
          name: 'system-logmails',
          path: 'logmails',
          component: () =>
            import('@/views/System/logMails/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'logMails_r' }, [
        {
          name: 'system-logmails-view',
          path: 'logmails/view/:id',
          component: () =>
            import('@/views/System/logMails/view')
        }
      ])
    ]
  }]