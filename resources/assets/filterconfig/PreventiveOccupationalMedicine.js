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
        },
        {
          url: '/selects/regionals',
          key: 'regionalsHeader',
          type: 'select',
          label: 'Regionales',
          header: true
        },
        {
          url: '/selects/employeesIdentifications',
          key: 'identifications',
          type: 'select',
          label: 'Identificación',
          header: true
        },
        {
          url: '/selects/employeesNames',
          key: 'names',
          type: 'select',
          label: 'Nombres',
          header: true
        },
        {
          url: '/selects/audiometry/severityGradeLeft',
          key: 'severity_grade_left',
          type: 'select',
          label: 'Grado de severidad Izquierdo',
          header: true
        },
        {
          url: '/selects/audiometry/severityGradeRight',
          key: 'severity_grade_right',
          type: 'select',
          label: 'Grado de severidad Derecho',
          header: true
        }
      ]
    },
    {
      name: 'biologicalmonitoring-audiometry-informs',
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
