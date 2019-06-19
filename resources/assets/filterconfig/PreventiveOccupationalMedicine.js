export default [
    {
      name: 'biologicalmonitoring-audiometry',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'Regionales'
        },
        {
          url: '/selects/headquarters',
          key: 'headquarters',
          type: 'select',
          label: 'Sedes'
        },
        {
          url: '/selects/processes',
          key: 'processes',
          type: 'select',
          label: 'Procesos'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'Áreas'
        },
        {
          url: '/selects/employeesDeal',
          key: 'deals',
          type: 'select',
          label: 'Negocios'
        },
        {
          url: '/selects/positions',
          key: 'positions',
          type: 'select',
          label: 'Cargo'
        },
        {
          url: '/selects/years/audiometry',
          key: 'years',
          type: 'select',
          label: 'Años'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
        }
      ]
    }
];
