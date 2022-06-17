export default [
    {
      name: 'system-logmails',
      filters: [
        {
          url: '/selects/companies',
          key: 'companies',
          type: 'select',
          label: 'Compañias'
        },
        {
            url: '/selects/modules',
            key: 'modules',
            type: 'select',
            label: 'Módulos'
        },
      ]
    },
    {
      name: 'system-userscompany',
      filters: [
        {
            url: '/selects/permissionsAlls',
            key: 'permissions',
            type: 'select',
            label: 'Permisos'
        },
      ]
    },
    {
      name: 'system-licenses',
      filters: [
        {
            url: '/selects/modules',
            key: 'modules',
            type: 'select',
            label: 'Módulos'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de creación',
        }
      ]
    }
]