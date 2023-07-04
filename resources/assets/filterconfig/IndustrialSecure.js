export default [
    {
      name: 'industrialsecure-dangermatrix-report',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          url: '/selects/tagsTypeProcess',
          key: 'macroprocesses',
          type: 'select',
          label: 'Macroprocesos'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'areas'
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
        },
        {
          url: '/selects/yearDangerMatrix',
          key: 'years',
          type: 'select',
          label: 'Año'
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
          label: 'regionals',
          column: 'regional',
          tag: false
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'headquarters',
          type: 'select',
          label: 'headquarters',
          column: 'headquarter',
          tag: false
        },
        {
          url: '/selects/dmReportMultiselect',
          key: 'processes',
          type: 'select',
          label: 'processes',
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
          label: 'areas',
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
    },
    {
      name: 'dangerousconditions-inspections',
      filters: [
        {
          url: '/selects/industrialSecurity/inspections',
          key: 'inspections',
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
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de creación',
        }
      ]
    },
    {
      name: 'dangerousconditions-inspections-qualification',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          url: '/selects/levelRisk',
          key: 'levelRisk',
          type: 'select',
          label: 'Nivel de Riesgo'
        },
        {
          url: '/selects/users',
          key: 'qualifiers',
          type: 'select',
          label: 'Usuario Calificador'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de calificación',
        }
      ]
    },
    {
      name: 'dangerousconditions-inspections-report',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          url: '/selects/industrialSecurity/inspections',
          key: 'inspections',
          type: 'select',
          label: 'Nombres'
        },
        {
          url: '/selects/themes',
          key: 'themes',
          type: 'select',
          label: 'Temas',
        },
        {
          url: '/selects/users',
          key: 'qualifiers',
          type: 'select',
          label: 'Usuario Calificador'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha',
        }
      ]
    },
    {
      name: 'dangerousconditions-report',
      filters: [
        {
          url: '/selects/industrialSecurity/conditions',
          key: 'conditions',
          type: 'select',
          label: 'Codición'
        },
        {
          url: '/selects/industrialSecurity/conditionTypes',
          key: 'conditionTypes',
          type: 'select',
          label: 'Tipo Condición'
        },
        {
          url: '/selects/actionPlanStates',
          key: 'states',
          type: 'select',
          label: 'Estados'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de creación',
        }
      ]
    },
    {
      name: 'dangerousconditions-report-informs',
      filters: [
        {
          url: '/selects/industrialSecurity/conditions',
          key: 'conditions',
          type: 'select',
          label: 'Condición'
        },
        {
          url: '/selects/industrialSecurity/rates',
          key: 'rates',
          type: 'select',
          label: 'Severidad'
        },
        {
          url: '/selects/users',
          key: 'users',
          type: 'select',
          label: 'Usuarios'
        },
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de creación',
        },

      ]
    },
    {
      name: 'dangerousconditions-inspections-request-firm-admin',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          url: '/selects/users',
          key: 'qualifiers',
          type: 'select',
          label: 'Usuario Calificador'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de calificación',
        },
        {
          url: '/selects/users',
          key: 'user_firm',
          type: 'select',
          label: 'Usuario Firmante'
        }
      ]
    },
    {
      name: 'dangerousconditions-inspections-request-firm',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          url: '/selects/users',
          key: 'qualifiers',
          type: 'select',
          label: 'Usuario Calificador'
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de calificación',
        },
      ]
    },
    {
      name: 'industrialsecure-riskmatrix-report',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          url: '/selects/tagsTypeProcessRiskMatrix',
          key: 'macroprocesses',
          type: 'select',
          label: 'Macroprocesos'
        },
        {
          url: '/selects/areas',
          key: 'areas',
          type: 'select',
          label: 'areas'
        },
        {
          url: '/selects/rmRisk',
          key: 'risks',
          type: 'select',
          label: 'Riesgos'
        }
      ]
    },
    {
      name: 'industrialsecure-riskmatrix-report-history',
      filters: [
        {
          url: '/selects/rmReportMultiselect',
          key: 'regionals',
          type: 'select',
          label: 'regionals',
          column: 'regional',
          tag: false
        },
        {
          url: '/selects/rmReportMultiselect',
          key: 'headquarters',
          type: 'select',
          label: 'headquarters',
          column: 'headquarter',
          tag: false
        },
        {
          url: '/selects/rmReportMultiselect',
          key: 'processes',
          type: 'select',
          label: 'processes',
          column: 'process',
          tag: false
        },
        {
          url: '/selects/rmReportMultiselect',
          key: 'macroprocesses',
          type: 'select',
          label: 'Macroprocesos',
          column: 'macroprocess',
          tag: false
        },
        {
          url: '/selects/rmReportMultiselect',
          key: 'areas',
          type: 'select',
          label: 'areas',
          column: 'area',
          tag: false
        },
        {
          url: '/selects/rmReportMultiselect',
          key: 'risks',
          type: 'select',
          label: 'Riesgos',
          column: 'risk',
          tag: false
        }
      ]
    },
    {
      name: 'industrialsecure-epp-report',
      filters: [
        {
          url: '/selects/eppElements',
          key: 'elements',
          type: 'select',
          label: 'Elementos'
        },
        {
          url: '/selects/tagsMarkEpp',
          key: 'marks',
          type: 'select',
          label: 'Marcas'
        },
        {
          url: '/selects/classElement',
          key: 'class',
          type: 'select',
          label: 'Clase'
        },
        {
          url: '/selects/eppLocations',
          key: 'location',
          type: 'select',
          label: 'Ubicación',
        },
        {
          url: '/selects/employeesId',
          key: 'employee',
          type: 'select',
          label: 'Empleado',
        }
      ]
    },
    {
      name: 'industrialsecure-accidents',
      filters: [
        {
          url: '/selects/accidents/identifications',
          key: 'identifications',
          type: 'select',
          label: 'Identificación'
        },
        {
          url: '/selects/accidents/names',
          key: 'names',
          type: 'select',
          label: 'Nombres'
        },
        {
          url: '/selects/accidents/razonSocial',
          key: 'razonSocial',
          type: 'select',
          label: 'Razón Social'
        },
        {
          url: '/selects/accidents/activityEconomic',
          key: 'activityEconomic',
          type: 'select',
          label: 'Actividad Económica',
        },
        {
          url: '/selects/accidents/cargo',
          key: 'cargo',
          type: 'select',
          label: 'Cargo',
        },
        {
          url: '/selects/accidents/siNo',
          key: 'causoMuerte',
          type: 'select',
          label: 'Causo Muerte',
        },
        {
          url: '/selects/sexs',
          key: 'sexs',
          type: 'select',
          label: 'Sexo',
        },
        {
          url: '/selects/accidents/siNo',
          key: 'dentroEmpresa',
          type: 'select',
          label: '¿Ocurrio dentro de la empresa?',
        },
        {
          url: '/selects/departaments',
          key: 'departament',
          type: 'select',
          label: 'Departamento',
        },
        {
          url: '/selects/municipalities',
          key: 'city',
          type: 'select',
          label: 'Ciudad',
        },
        {
          url: '/selects/accidents/agents',
          key: 'agent',
          type: 'select',
          label: 'Agentes',
        },
        {
          url: '/selects/accidents/mechanisms',
          key: 'mechanism',
          type: 'select',
          label: 'Mecanismos',
        },
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de accidente',
        }
      ]
    },
    {
      name: 'industrialsecure-accidents-report',
      filters: [
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de accidente',
        }
      ]
    },
    {
      name: 'industrialsecure-epp-delivery',
      filters: [
        {
          key: 'dateRange',
          type: 'dateRange',
          label: 'Fecha de creación',
        }
      ]
    },
    {
      name: 'industrialsecure-dangermatrix-log-qualification',
      filters: [
        {
          url: '/selects/regionals',
          key: 'regionals',
          type: 'select',
          label: 'regionals'
        },
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
          url: '/selects/dmDangers',
          key: 'dangers',
          type: 'select',
          label: 'Peligros'
        },
        {
          url: '/selects/dmActivities',
          key: 'activities',
          type: 'select',
          label: 'Actividades'
        }
      ]
    },
];
