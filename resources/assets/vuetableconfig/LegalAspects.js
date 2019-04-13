export default [
    {
        name: 'legalaspects-typesrating',
        fields: [
            { name: 'sau_ct_types_ratings.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_ct_types_ratings.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
            { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
        ],
        'controlls': [{
            type: 'push',
            buttons: [{
            config: {
                color: 'outline-success',
                borderless: true,
                icon: 'ion ion-md-create',
                title: 'Editar'
            },
            data: {
                routePush: { name: 'legalaspects-typesrating-edit' },
                id: 'id',
            },
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'legalaspects-typesrating-view' },
                id: 'id',
            },
            }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/legalAspects/typeRating/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tipo de calificación __name__'
            },
            }],
        }],
        configuration: {
            urlData: '/legalAspects/typeRating/data',
            filterColumns: true,
        }
    },
    {
        name: 'legalaspects-evaluations',
        fields: [
            { name: 'sau_ct_evaluations.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_ct_evaluations.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_evaluations.type', data: 'type', title: 'Tipo', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_evaluations.created_at', data: 'created_at', title: 'Fecha creación', sortable: true, searchable: true, detail: false, key: false },
            { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
        ],
        'controlls': [{
            type: 'push',
            buttons: [{
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-create',
                    title: 'Editar'
                },
                data: {
                    routePush: { name: 'legalaspects-evaluations-edit' },
                    id: 'id',
                },
            }, {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'legalaspects-evaluations-view' },
                    id: 'id',
                },
            }, {
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-clipboard',
                    title: 'Realizar Evaluación'
                },
                data: {
                    routePush: { name: 'legalaspects-evaluations-evaluate' },
                    id: 'id',
                },
            }, {
              config: {
                  color: 'outline-info',
                  borderless: true,
                  icon: 'ion ion-md-list',
                  title: 'Ver Evaluaciones Realizadas'
              },
              data: {
                  routePush: { name: 'legalaspects-evaluations-contracts' },
                  id: 'id',
              },
          }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/legalAspects/evaluation/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar la evaluación __name__'
            },
            }],
        }],
        configuration: {
            urlData: '/legalAspects/evaluation/data',
            filterColumns: true,
            configNameFilter: 'legalaspects-evaluations'
        }
    },
    {
      name: 'legalAspects-fileUpload',
      fields: [
        { name: 'sau_ct_file_upload_contracts_leesse.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_ct_file_upload_contracts_leesse.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ct_file_upload_contracts_leesse.expirationDate', data: 'expirationDate', title: 'Fecha Vencimiento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_users.name', data: 'Usuario', title: 'Nombre Usuario', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ct_file_upload_contracts_leesse.created_at', data: 'created_at', title: 'Fecha Creación', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ct_file_upload_contracts_leesse.updated_at', data: 'updated_at', title: 'Fecha Actualización', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
      ],
      'controlls': [{
        type: 'push',
        buttons: [{
          config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-create',
            title: 'Editar'
          },
          data: {
            routePush: { name: 'legalaspects-upload-files-edit' },
            id: 'id',
          }
        }, {
          config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
          },
          data: {
            routePush: { name: 'legalaspects-upload-files-view' },
            id: 'id',
          }
        }]
      },
      {
        type: 'base',
        buttons: [{
          name: 'delete',
          data: {
            action: '/legalAspects/fileUpload/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el archivo del __name__'
          }
        }],
      }],
      configuration: {
        urlData: '/legalAspects/fileUpload/data',
        filterColumns: true,
      }
    }, 
    {
      name: 'legalaspects-evaluations-contracts',
      fields: [
          { name: 'sau_ct_evaluation_contract.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
          { name: 'sau_ct_information_contract_lessee.nit', data: 'nit', title: 'NIT', sortable: true, searchable: true, detail: false, key: false },
          { name: 'sau_ct_information_contract_lessee.social_reason', data: 'social_reason', title: 'Razón social', sortable: true, searchable: true, detail: false, key: false },
          { name: 'sau_ct_evaluation_contract.evaluation_date', data: 'evaluation_date', title: 'Fecha evaluación', sortable: true, searchable: true, detail: false, key: false },
          { name: 'sau_users.name', data: 'name', title: 'Calificador', sortable: true, searchable: true, detail: false, key: false },
          { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
      ],
      'controlls': [{
            type: 'push',
            buttons: [{
            config: {
                color: 'outline-success',
                borderless: true,
                icon: 'ion ion-md-create',
                title: 'Editar'
            },
            data: {
                routePush: { name: 'legalaspects-evaluations-contracts-edit' },
                id: 'id',
            },
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'legalaspects-evaluations-contracts-view' },
                id: 'id',
            },
            }]
        },
        {
            type: 'base',
            buttons: [],
        }],
      configuration: {
          urlData: '/legalAspects/evaluationContract/data',
          filterColumns: true,
          configNameFilter: 'legalaspects-evaluations-contracts'
      }
    },
    {
      name: 'legalaspects-evaluations-lessee',
      fields: [
          { name: 'sau_ct_evaluation_contract.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
          { name: 'sau_ct_evaluation_contract.evaluation_date', data: 'evaluation_date', title: 'Fecha evaluación', sortable: true, searchable: true, detail: false, key: false },
          { name: 'sau_users.name', data: 'name', title: 'Calificador', sortable: true, searchable: true, detail: false, key: false },
          { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
      ],
      'controlls': [{
            type: 'push',
            buttons: [{
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'legalaspects-evaluations-contracts-view' },
                id: 'id',
            },
            }]
        },
        {
            type: 'base',
            buttons: [],
        }],
      configuration: {
          urlData: '/legalAspects/evaluationContract/data',
          filterColumns: true,
          configNameFilter: 'legalaspects-evaluations-contracts'
      }
    },
    {
        name: 'legalaspects-evaluations-contracts-histories',
        fields: [
            { name: 'sau_ct_evaluation_contract_histories.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_users.name', data: 'name', title: 'Responsable', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_evaluation_contract_histories.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: false, detail: false, key: false },
        ],
        'controlls': [{
                type: 'push',
                buttons: []
            },
            {
                type: 'base',
                buttons: [],
            }],
        configuration: {
            urlData: '/legalAspects/evaluationContractHistory/data',
            filterColumns: true,
        }
    },
    {
        name: 'legalaspects-evaluations-reports',
        fields: [
            { name: 'subobjective', data: 'subobjective', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'objective', data: 'objective', title: 'Objetivo', sortable: true, searchable: false, detail: false, key: false },
            { name: 'subobjective', data: 'subobjective', title: 'Subobjetivo', sortable: true, searchable: false, detail: false, key: false },
            { name: 't_evaluations', data: 't_evaluations', title: '# Evaluaciones', sortable: true, searchable: false, detail: false, key: false },
            { name: 't_cumple', data: 't_cumple', title: '# Cumplimietos', sortable: true, searchable: false, detail: false, key: false },
            { name: 't_no_cumple', data: 't_no_cumple', title: '# No Cumplimientos', sortable: true, searchable: false, detail: false, key: false },
            { name: 'p_cumple', data: 'p_cumple', title: '% Cumplimiento', sortable: true, searchable: false, detail: false, key: false },
            { name: 'p_no_cumple', data: 'p_no_cumple', title: '% No Cumplimiento', sortable: true, searchable: false, detail: false, key: false }
        ],
        'controlls': [{
                type: 'push',
                buttons: []
            },
            {
                type: 'base',
                buttons: [],
            }],
        configuration: {
            urlData: '/legalAspects/evaluationContract/report',
            filterColumns: false,
            configNameFilter: 'legalaspects-evaluations'
        }
    },
]