export default [
    {
      name: 'biologicalmonitoring-audiometry',
      filters: [
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
          key: 'regionals',
          type: 'select',
          label: 'Regionales',
          header: true
        },
        {
          url: '/selects/employeesDeal',
          key: 'deals',
          type: 'select',
          label: 'Negocios',
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
          key: 'regionals',
          type: 'select',
          label: 'Regionales',
          header: true
        },
        {
          url: '/selects/employeesDeal',
          key: 'deals',
          type: 'select',
          label: 'Negocios',
          header: true
        },
      ]
    },
    {
      name: 'reinstatements-checks',
      filters: [
        {
          url: '/selects/employeesIdentifications',
          key: 'identifications',
          type: 'select',
          label: 'Identificación'
        }, 
        {
          url: '/selects/employeesNames',
          key: 'names',
          type: 'select',
          label: 'Nombres'
        },
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'Regionales'
        },
        {
          url: '/selects/businesses',
          key: 'businesses',
          type: 'select',
          label: 'Centro de costos'
        },
        {
          url: '/selects/diseaseOrigin',
          key: 'diseaseOrigin',
          type: 'select',
          label: 'Origen de enfermedad'
        }, 
        {
          url: '/selects/nextFollowDays',
          key: 'nextFollowDays',
          type: 'select',
          label: 'Próximo seguimiento (Dias)'
        }, 
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
        },
      ]
    },
    {
      name: 'biologicalmonitoring-musculoskeletalAnalysis',
      filters: [ 
        {
          url: '/selects/branchOffice',
          key: 'branchOffice',
          type: 'select',
          label: 'Sucursales',
          header:true
        },
        {
          url: '/selects/bm_musculoskeletalCompany',
          key: 'companies',
          type: 'select',
          label: 'Compañias',
          header:true
        },
        {
          url: '/selects/consolidatedPersonalRiskCriterion',
          key: 'consolidatedPersonalRiskCriterion',
          type: 'select',
          label: 'Consolidado Riesgo Personal (Criterio)',
          header:true
        }
      ]
    }
];
