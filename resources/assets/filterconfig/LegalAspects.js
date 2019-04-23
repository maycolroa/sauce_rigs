export default [
    {
      name: 'legalaspects-evaluations',
      filters: [
        {
          url: '/selects/evaluations/objectives',
          key: 'evaluationsObjectives',
        },
        {
          url: '/selects/evaluations/subobjectives',
          key: 'evaluationsSubobjectives',
        },
        {
          key: 'dateRange'
        }
      ]
    },
    {
      name: 'legalaspects-evaluations-contracts',
      filters: [
        {
          key: 'dateRange'
        }
      ]
    },
    {
      name: 'legalAspects-fileUpload',
      filters: [
        {
          url: '/selects/contracts/sectionCategoryItems',
          key: 'items',
        }
      ]
    },
];
