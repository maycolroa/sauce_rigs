export default [
    {
      name: 'biologicalmonitoring-audiometry',
      filters: [
        {
          url: '/selects/headquarters',
          key: 'headquarters',
          type: 'select',
          label: 'headquarters'
        },
        {
          url: '/selects/processes',
          key: 'processes',
          type: 'select',
          label: 'processes'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'areas'
        },
        {
          url: '/selects/positions',
          key: 'positions',
          type: 'select',
          label: 'positions'
        },
        {
          url: '/selects/years/audiometry',
          key: 'years',
          type: 'select',
          label: 'Años'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
        },
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals',
          header: true
        },
        {
          url: '/selects/employeesDeal',
          key: 'deals',
          type: 'select',
          label: 'Negocios',
          header: true
        },
        {
          url: '/selects/employeesIdentifications',
          key: 'identifications',
          type: 'select',
          label: 'Identificación',
          header: true
        },
        {
          url: '/selects/employeesNames',
          key: 'names',
          type: 'select',
          label: 'Nombres',
          header: true
        },
        {
          url: '/selects/audiometry/severityGradeLeft',
          key: 'severity_grade_left',
          type: 'select',
          label: 'Grado de severidad Izquierdo',
          header: true
        },
        {
          url: '/selects/audiometry/severityGradeRight',
          key: 'severity_grade_right',
          type: 'select',
          label: 'Grado de severidad Derecho',
          header: true
        }
      ]
    },
    {
      name: 'biologicalmonitoring-audiometry-informs',
      filters: [
        {
          url: '/selects/headquarters',
          key: 'headquarters',
          type: 'select',
          label: 'headquarters'
        },
        {
          url: '/selects/processes',
          key: 'processes',
          type: 'select',
          label: 'processes'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'areas'
        },
        {
          url: '/selects/positions',
          key: 'positions',
          type: 'select',
          label: 'positions'
        },
        {
          url: '/selects/years/audiometry',
          key: 'years',
          type: 'select',
          label: 'Años'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
        },
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals',
          header: true
        },
        {
          url: '/selects/employeesDeal',
          key: 'deals',
          type: 'select',
          label: 'Negocios',
          header: true
        },
      ]
    },
    {
      name: 'reinstatements-checks',
      filters: [
        {
          url: '/selects/employeesIdentifications',
          key: 'identifications',
          type: 'select',
          label: 'Identificación'
        }, 
        {
          url: '/selects/employeesNames',
          key: 'names',
          type: 'select',
          label: 'Nombres'
        },
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
        {
          url: '/selects/businesses',
          key: 'businesses',
          type: 'select',
          label: 'businesses'
        },
        {
          url: '/selects/diseaseOrigin',
          key: 'diseaseOrigin',
          type: 'select',
          label: 'Origen de enfermedad'
        },
        {
          url: '/selects/reincYears',
          key: 'years',
          type: 'select',
          label: 'Años'
        },
        {
          url: '/selects/reincSveAssociateds',
          key: 'sveAssociateds',
          type: 'select',
          label: 'SVE Asociados'
        },
        {
          url: '/selects/reincMedicalCertificates',
          key: 'medicalCertificates',
          type: 'select',
          label: 'Certificado médico UEAC'
        },
        {
          url: '/selects/reincRelocatedTypes',
          key: 'relocatedTypes',
          type: 'select',
          label: 'Tipos de reintegro'
        },
        {
          url: '/selects/nextFollowDays',
          key: 'nextFollowDays',
          type: 'select',
          label: 'Próximo seguimiento (Dias)'
        }, 
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
        },
      ]
    },
    {
      name: 'biologicalmonitoring-musculoskeletalAnalysis',
      filters: [ 
        {
          url: '/selects/branchOffice',
          key: 'branchOffice',
          type: 'select',
          label: 'Regional o Planta',
          header:true
        },
        {
          url: '/selects/bm_musculoskeletalCompany',
          key: 'companies',
          type: 'select',
          label: 'Compañias',
          header:true
        },
        {
          url: '/selects/consolidatedPersonalRiskCriterion',
          key: 'consolidatedPersonalRiskCriterion',
          type: 'select',
          label: 'Consolidado Riesgo Personal (Criterio)',
          header:true
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
          header:true
        }
      ]
    },
    {
      name: 'biologicalmonitoring-respiratoryAnalysis',
      filters: [ 
        {
          url: '/selects/respiratory/regional',
          key: 'regional',
          type: 'select',
          label: 'Regional o Planta',
          header:true
        },
        {
          url: '/selects/respiratory/deal',
          key: 'deal',
          type: 'select',
          label: 'Negocio',
          header:true
        },
        {
          url: '/selects/respiratory/interpretation',
          key: 'interpretation',
          type: 'select',
          label: 'Interpretación',
          header:true
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Rango de fecha',
          header:true
        }
      ]
    }
];
