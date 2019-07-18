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
    }
]