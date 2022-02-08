export default [
  {
    name: 'administrative-actionplans',
    filters: [
      {
        url: '/selects/responsiblesFilter',
        key: 'responsibles',
        type: 'select',
        label: 'Responsables'
      },
      {
        url: '/selects/actionPlanModules',
        key: 'modules',
        type: 'select',
        label: 'Módulos'
      },
      {
        url: '/selects/actionPlanStates/all',
        key: 'states',
        type: 'select',
        label: 'Estados'
      },
      {
        url: '/selects/regionals',
        key: 'regionals',
        type: 'select',
        label: 'regionals'
      },
      {
        url: '/selects/users',
        key: 'creators',
        type: 'select',
        label: 'Usuario Creador'
      },
      {
        key: 'dateRange',
        type: 'dateRange',
        label: 'Fecha de vencimiento',
      }
    ]
  },
  {
    name: 'administrative-users',
    filters: [
      {
        url: '/selects/roles',
        key: 'roles',
        type: 'select',
        label: 'Roles'
      }
    ] 
  }
];
