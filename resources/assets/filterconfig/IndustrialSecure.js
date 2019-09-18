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
    }
];
