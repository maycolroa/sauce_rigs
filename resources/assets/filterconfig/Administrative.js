export default [
    {
      name: 'administrative-actionplans',
      filters: [
        {
            url: '/selects/responsiblesFilter',
            key: 'responsibles',
        },
        {
            url: '/selects/actionPlanModules',
            key: 'modules',
        },
        {
            url: '/selects/actionPlanStates',
            key: 'states',
        }
      ]
    }
];
