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
        url: '/selects/actionPlanStates',
        key: 'states',
        type: 'select',
        label: 'Estados'
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
