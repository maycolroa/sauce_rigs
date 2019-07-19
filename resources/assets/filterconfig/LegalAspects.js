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
          url: '/selects/contractors',
          key: 'contracts',
          type: 'select',
          label: 'Contratistas',
          permission: 'contracts_r'
        },
        {
          url: '/selects/contracts/sectionCategoryItems',
          key: 'items',
          type: 'select',
          label: 'Items'
        },
        {
          key: 'dateCreate',
          type: 'dateRange',
          label: 'Fecha de creación',
        },
        {
          key: 'dateUpdate',
          type: 'dateRange',
          label: 'Fecha de actualización',
        }
      ]
    },
    {
      name: 'legalAspects-legalMatrix-laws',
      filters: [
        {
          url: '/selects/legalMatrix/lawsTypes',
          key: 'lawTypes',
          type: 'select',
          label: 'Tipo de norma'
        },
        {
          url: '/selects/legalMatrix/riskAspects',
          key: 'riskAspects',
          type: 'select',
          label: 'Riesgo/Aspecto Ambiental'
        },
        {
          url: '/selects/legalMatrix/entities',
          key: 'entities',
          type: 'select',
          label: 'Entes'
        },
        {
          url: '/selects/legalMatrix/sstRisks',
          key: 'sstRisks',
          type: 'select',
          label: 'Temas SST'
        },
        {
          url: '/selects/legalMatrix/systemApplySystem',
          key: 'systemApply',
          type: 'select',
          label: 'Sistema que aplica'
        },
        {
          url: '/selects/legalMatrix/lawNumbersSystem',
          key: 'lawNumbers',
          type: 'select',
          label: 'Número de norma'
        },
        {
          url: '/selects/legalMatrix/lawYearsSystem',
          key: 'lawYears',
          type: 'select',
          label: 'Años'
        },
        {
          url: '/selects/legalMatrix/repealed',
          key: 'repealed',
          type: 'select',
          label: 'Derogada'
        }
      ]
    },
    {
      name: 'legalAspects-legalMatrix-laws-company',
      filters: [
        {
          url: '/selects/legalMatrix/lawsTypes',
          key: 'lawTypes',
          type: 'select',
          label: 'Tipo de norma'
        },
        {
          url: '/selects/legalMatrix/riskAspects',
          key: 'riskAspects',
          type: 'select',
          label: 'Riesgo/Aspecto Ambiental'
        },
        {
          url: '/selects/legalMatrix/entities',
          key: 'entities',
          type: 'select',
          label: 'Entes'
        },
        {
          url: '/selects/legalMatrix/sstRisks',
          key: 'sstRisks',
          type: 'select',
          label: 'Temas SST'
        },
        {
          url: '/selects/legalMatrix/systemApplyCompany',
          key: 'systemApply',
          type: 'select',
          label: 'Sistema que aplica'
        },
        {
          url: '/selects/legalMatrix/lawNumbersCompany',
          key: 'lawNumbers',
          type: 'select',
          label: 'Número de norma'
        },
        {
          url: '/selects/legalMatrix/lawYearsCompany',
          key: 'lawYears',
          type: 'select',
          label: 'Años'
        },
        {
          url: '/selects/legalMatrix/repealed',
          key: 'repealed',
          type: 'select',
          label: 'Derogada'
        }
      ]
    },
    {
      name: 'legalAspects-legalMatrix-law-qualify',
      filters: [
        {
          url: '/selects/legalMatrix/lawsTypes',
          key: 'lawTypes',
          type: 'select',
          label: 'Tipo de norma'
        },
        {
          url: '/selects/legalMatrix/riskAspects',
          key: 'riskAspects',
          type: 'select',
          label: 'Riesgo/Aspecto Ambiental'
        },
        {
          url: '/selects/legalMatrix/entities',
          key: 'entities',
          type: 'select',
          label: 'Entes'
        },
        {
          url: '/selects/legalMatrix/sstRisks',
          key: 'sstRisks',
          type: 'select',
          label: 'Temas SST'
        },
        {
          url: '/selects/legalMatrix/systemApply',
          key: 'systemApply',
          type: 'select',
          label: 'Sistema que aplica'
        },
        {
          url: '/selects/legalMatrix/lawNumbers',
          key: 'lawNumbers',
          type: 'select',
          label: 'Número de norma'
        },
        {
          url: '/selects/legalMatrix/lawYears',
          key: 'lawYears',
          type: 'select',
          label: 'Años'
        },
        {
          url: '/selects/legalMatrix/repealed',
          key: 'repealed',
          type: 'select',
          label: 'Derogada'
        },
        {
          url: '/selects/legalMatrix/responsibles',
          key: 'responsibles',
          type: 'select',
          label: 'Responsables'
        },
        {
          url: '/selects/legalMatrix/states',
          key: 'states',
          type: 'select',
          label: 'Estado de evaluación'
        }
      ]
    },
    {
      name: 'legalaspects-contractor',
      filters: [
        {
          key: 'rangePC',
          type: 'numberRange',
          label: 'Rango de cumplimiento (%)',
        }
      ]
    }
];
