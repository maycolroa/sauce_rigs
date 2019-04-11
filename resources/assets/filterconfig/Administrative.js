export default [
    {
      name: 'administrative-actionplans',
      filters: [
        {
            url: '/selects/usersAll',
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
