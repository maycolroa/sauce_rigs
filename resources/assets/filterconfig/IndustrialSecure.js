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
    }
];
