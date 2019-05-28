export default [
    {
      name: 'legalaspects-evaluations',
      filters: [
        {
          url: '/selects/evaluations/objectives',
          key: 'evaluationsObjectives',
          type: 'select',
          label: 'Objetivos'
        },
        {
          url: '/selects/evaluations/subobjectives',
          key: 'evaluationsSubobjectives',
          type: 'select',
          label: 'Subobjetivos'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
        }
      ]
    },
    {
      name: 'legalaspects-evaluations-contracts',
      filters: [
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
        }
      ]
    },
    {
      name: 'legalAspects-fileUpload',
      filters: [
        {
          url: '/selects/contracts/sectionCategoryItems',
          key: 'items',
          type: 'select',
          label: 'Items'
        }
      ]
    },
];
