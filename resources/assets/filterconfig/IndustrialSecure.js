export default [
    {
      name: 'industrialsecure-dangermatrix-report',
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
          url: '/selects/tagsTypeProcess',
          key: 'macroprocesses',
          type: 'select',
          label: 'Macroprocesos'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'Áreas'
        },
        {
          url: '/selects/dmDangers',
          key: 'dangers',
          type: 'select',
          label: 'Peligros'
        },
        {
          url: '/selects/tagsDangerDescription',
          key: 'dangerDescription',
          type: 'select',
          label: 'Descripción del peligro'
        }
      ]
    },
    {
      name: 'industrialsecure-dangermatrix-report-history',
      filters: [
        {
          url: '/selects/dmReportMultiselect',
          key: 'regionals',
          type: 'select',
          label: 'Regionales',
          column: 'regional',
          tag: false
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'headquarters',
          type: 'select',
          label: 'Sedes',
          column: 'headquarter',
          tag: false
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'processes',
          type: 'select',
          label: 'Procesos',
          column: 'process',
          tag: false
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'macroprocesses',
          type: 'select',
          label: 'Macroprocesos',
          column: 'macroprocess',
          tag: true
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'areas',
          type: 'select',
          label: 'Áreas',
          column: 'area',
          tag: false
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'dangers',
          type: 'select',
          label: 'Peligros',
          column: 'danger',
          tag: false
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'dangerDescription',
          type: 'select',
          label: 'Descripción del peligro',
          column: 'danger_description',
          tag: true
        }
      ]
    },
    {
      name: 'dangerousconditions-inspections',
      filters: [
        {
          url: '/selects/industrialSecurity/inspections',
          key: 'inspections',
          type: 'select',
          label: 'Nombres'
        },
        {
          url: '/selects/headquarters',
          key: 'headquarters',
          type: 'select',
          label: 'Sedes'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'Áreas'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de creación',
        }
      ]
    },
    {
      name: 'dangerousconditions-inspections-qualification',
      filters: [
        {
          url: '/selects/headquarters',
          key: 'headquarters',
          type: 'select',
          label: 'Sedes'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'Áreas'
        },
        {
          url: '/selects/users',
          key: 'qualifiers',
          type: 'select',
          label: 'Calificador'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de calificación',
        }
      ]
    },
    {
      name: 'dangerousconditions-inspections-report',
      filters: [
        {
          url: '/selects/headquarters',
          key: 'headquarters',
          type: 'select',
          label: 'Sedes'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'Áreas'
        },
        {
          url: '/selects/themes',
          key: 'themes',
          type: 'select',
          label: 'Temas',
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha',
        }
      ]
    },
    {
      name: 'dangerousconditions-report',
      filters: [
        {
          url: '/selects/industrialSecurity/conditions',
          key: 'conditions',
          type: 'select',
          label: 'Codición'
        },
        {
          url: '/selects/industrialSecurity/conditionTypes',
          key: 'conditionTypes',
          type: 'select',
          label: 'Tipo Condición'
        },
        {
          url: '/selects/actionPlanStates',
          key: 'states',
          type: 'select',
          label: 'Estados'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de creación',
        }
      ]
    }
];
