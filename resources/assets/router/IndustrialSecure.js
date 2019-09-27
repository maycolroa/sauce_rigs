import LayoutModules from '@/views/layoutModules'
import Home from '@/views/home'
import { middleware } from 'vue-router-middleware'

export default [{
    path: '/industrialsecure',
    component: LayoutModules,
    children: [
      {
        path: '',
        name: 'industrialsecure',
        component: Home,
      },
      ...middleware({ 'check-permission': 'activities_r' }, [
        {
          name: 'industrialsecure-activities',
          path: 'activities',
          component: () =>
            import('@/views/IndustrialSecure/activities/index')
        }
      ]), 
      ...middleware({ 'check-permission': 'activities_c' }, [
        {
          name: 'industrialsecure-activities-create',
          path: 'activities/create',
          component: () =>
            import('@/views/IndustrialSecure/activities/create')
        }
      ]), 
      ...middleware({ 'check-permission': 'activities_u' }, [
        {
          name: 'industrialsecure-activities-edit',
          path: 'activities/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/activities/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'activities_r' }, [
        {
          name: 'industrialsecure-activities-view',
          path: 'activities/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/activities/view')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_r' }, [ 
        {
          name: 'industrialsecure-dangers',
          path: 'dangers',
          component: () =>
            import('@/views/IndustrialSecure/dangers/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_c' }, [ 
        {
          name: 'industrialsecure-dangers-create',
          path: 'dangers/create',
          component: () =>
            import('@/views/IndustrialSecure/dangers/create')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_u' }, [ 
        {
          name: 'industrialsecure-dangers-edit',
          path: 'dangers/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangers/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'dangers_r' }, [ 
        {
          name: 'industrialsecure-dangers-view',
          path: 'dangers/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangers/view')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [ 
        {
          name: 'industrialsecure-dangermatrix',
          path: 'dangermatrix',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/index')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-create',
          path: 'dangermatrix/create',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/create')
        },
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_u' }, [
        {
          name: 'industrialsecure-dangermatrix-edit',
          path: 'dangermatrix/edit/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/edit')
        }
      ]), 
      ...middleware({ 'check-permission': 'dangerMatrix_r' }, [
        {
          name: 'industrialsecure-dangermatrix-view',
          path: 'dangermatrix/view/:id',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/view')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_view_report' }, [
        {
          name: 'industrialsecure-dangermatrix-report',
          path: 'dangermatrix/report',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/report')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags',
          path: 'dangermatrix/tags',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/tags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-administrative-controls',
          path: 'dangermatrix/tags/administrativecontrols',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-engineering-controls',
          path: 'dangermatrix/tags/engineeringcontrols',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-possible-consequences-danger',
          path: 'dangermatrix/tagspossibleconsequencesdanger',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-substitutions',
          path: 'dangermatrix/tags/substitutions',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-warning-signage',
          path: 'dangermatrix/tags/warningsignage',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-epp',
          path: 'dangermatrix/tags/epp',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-participants',
          path: 'dangermatrix/tags/participants',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_c' }, [
        {
          name: 'industrialsecure-dangermatrix-tags-danger-description',
          path: 'dangermatrix/tags/dangerdescription',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/indexTags')
        }
      ]),
      ...middleware({ 'check-permission': 'dangerMatrix_view_report' }, [
        {
          name: 'industrialsecure-dangermatrix-report-history',
          path: 'dangermatrix/report/history',
          component: () =>
            import('@/views/IndustrialSecure/dangerMatrix/reportHistory')
        }
      ]),
      {
        name: 'industrialsecure-dangerousconditions',
        path: 'dangerousconditions',
        component: () =>
            import('@/views/IndustrialSecure/dangerousConditions/index')
      },
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections',
          path: 'dangerousconditions/inspections/index',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/index')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'dangerousconditions-inspections-create',
          path: 'dangerousconditions/inspections/create',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/create')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_r' }, [
        {
          name: 'dangerousconditions-inspections-view',
          path: 'dangerousconditions/inspections/view/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/view')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_u' }, [
        {
          name: 'dangerousconditions-inspections-edit',
          path: 'dangerousconditions/inspections/edit/:id',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/edit')
        }
      ]),
      ...middleware({ 'check-permission': 'ph_inspections_c' }, [
        {
          name: 'dangerousconditions-inspections-clone',
          path: 'dangerousconditions/inspections/clone',
          component: () =>
              import('@/views/IndustrialSecure/dangerousConditions/inspections/clone')
        }
      ]),
      /*{
        name: 'industrialsecure-inspections',
        path: 'inspections',
        component: () => import('@/views/IndustrialSecure/inspections/index')
      },
      ...middleware({ 'check-permission': 'inspect_conditionsReports_r' }, [
        {
          name: 'inspections-conditionsReports',
          path: 'inspections/conditionsReports',
          component: () =>
              import('@/views/IndustrialSecure/inspections/conditionsReports/index')
        }
      ]),
      ...middleware({ 'check-permission': 'inspect_conditionsReports_r' }, [
        {
          name: 'inspections-conditionsReports-view',
          path: 'inspections/conditionsReports/view',
          component: () =>
              import('@/views/IndustrialSecure/inspections/conditionsReports/view')
        }
      ]),
      ...middleware({ 'check-permission': 'inspect_conditionsReports_u' }, [
        {
          name: 'inspections-conditionsReports-edit',
          path: 'inspections/conditionsReports/edit',
          component: () =>
              import('@/views/IndustrialSecure/inspections/conditionsReports/edit')
        }
      ])*/

    ]
  }]