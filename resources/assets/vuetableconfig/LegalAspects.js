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
            permission: 'contracts_typesQualification_u'
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
            permission: 'contracts_typesQualification_r'
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
            permission: 'contracts_typesQualification_d'
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
                permission: 'contracts_evaluations_u'
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
                permission: 'contracts_evaluations_r'
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
                permission: 'contracts_evaluations_perform_evaluation'
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
              permission: 'contracts_evaluations_view_evaluations_made'
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
            permission: 'contracts_evaluations_d'
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
        { name: 'sau_ct_file_upload_contracts_leesse.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
          },
          permission: 'contracts_uploadFiles_u'
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
          },
          permission: 'contracts_uploadFiles_r'
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
          },
          permission: 'contracts_uploadFiles_d'
        }],
      }],
      configuration: {
        urlData: '/legalAspects/fileUpload/data',
        filterColumns: true,
        configNameFilter: 'legalAspects-fileUpload'
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
            permission: 'contracts_evaluations_edit_evaluations_made'
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
            permission: 'contracts_evaluations_view_evaluations_made'
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
            permission: 'contracts_evaluations_view_evaluations_made'
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
            { name: 'sau_ct_evaluation_contract_histories.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
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
    {
        name: 'legalaspects-contractor',
        fields: [
            { name: 'sau_ct_information_contract_lessee.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_ct_information_contract_lessee.nit', data: 'nit', title: 'Nit', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_information_contract_lessee.social_reason', data: 'social_reason', title: 'Razón social', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_information_contract_lessee.type', data: 'type', title: 'Tipo', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_information_contract_lessee.high_risk_work', data: 'high_risk_work', title: '¿Alto riesgo?', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_information_contract_lessee.active', data: 'active', title: '¿Activo?', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_list_check_resumen.total_standard', data: 'total_standard', title: 'Estándares', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_list_check_resumen.total_c', data: 'total_c', title: '#Cumple', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_list_check_resumen.total_nc', data: 'total_nc', title: '#No Cumple', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_list_check_resumen.total_sc', data: 'total_sc', title: '#Sin Calificar', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_list_check_resumen.total_p_c', data: 'total_p_c', title: '%Cumple', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_ct_list_check_resumen.total_p_nc', data: 'total_p_nc', title: '%No Cumple', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'legalaspects-contractor-edit' },
                    id: 'id',
                },
                permission: 'contracts_u'
              }, {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'legalaspects-contractor-view' },
                    id: 'id',
                },
                permission: 'contracts_r'
              }, {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-list',
                    title: 'Ver Lista de estándares mínimos'
                },
                data: {
                    routePush: { name: 'legalaspects-contracts-view-list-check' },
                    id: 'id',
                },
                permission: 'contracts_view_list_standards'
            }]
          },
          {
            type: 'base',
            buttons: [
                {
                    name: 'retrySendMail',
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'ion ion-ios-mail',
                        title: 'Reenviar correo de bienvenida'
                    },
                    data: {
                        action: '/legalAspects/contracts/retrySendMail/',
                        id: 'id',
                        messageConfirmation: 'Esta seguro de reenviar el correo de bienvenida a __social_reason__'
                    },
                    permission: 'contracts_resend_welcome_email'
                }
            ],
          }],
        configuration: {
            urlData: '/legalAspects/contracts/data',
            filterColumns: true,
            configNameFilter: 'legalaspects-contractor'
        }
    },
    {
        name: 'legalaspects-contractor-list-check-history',
        fields: [
            { name: 'sau_ct_lisk_check_change_histories.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_users.name', data: 'name', title: 'Responsable', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_ct_lisk_check_change_histories.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: false, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/legalAspects/contractsListCheckHistory/data',
            filterColumns: true,
        }
    },
    {
        name: 'legalaspects-lm-interests',
        fields: [
            { name: 'sau_lm_interests.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_lm_interests.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'legalaspects-lm-interest-edit' },
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
                    routePush: { name: 'legalaspects-lm-interest-view' },
                    id: 'id',
                },
            }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/legalAspects/legalMatrix/interest/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el interes __name__'
            },
            }],
        }],
        configuration: {
            urlData: '/legalAspects/legalMatrix/interest/data',
            filterColumns: true,
        }
    },
    {
        name: 'legalaspects-lm-riskaspect',
        fields: [
            { name: 'sau_lm_risks_aspects.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_lm_risks_aspects.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'legalaspects-lm-riskaspect-edit' },
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
                    routePush: { name: 'legalaspects-lm-riskaspect-view' },
                    id: 'id',
                },
            }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/legalAspects/legalMatrix/riskAspect/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el Riesgo/Aspecto ambiental __name__'
            },
            }],
        }],
        configuration: {
            urlData: '/legalAspects/legalMatrix/riskAspect/data',
            filterColumns: true,
        }
    },
    {
        name: 'legalaspects-lm-sstrisk',
        fields: [
            { name: 'sau_lm_sst_risks.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_lm_sst_risks.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'legalaspects-lm-sstrisk-edit' },
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
                    routePush: { name: 'legalaspects-lm-sstrisk-view' },
                    id: 'id',
                },
            }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/legalAspects/legalMatrix/sstRisk/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el Riesgo SST __name__'
            },
            }],
        }],
        configuration: {
            urlData: '/legalAspects/legalMatrix/sstRisk/data',
            filterColumns: true,
        }
    },
    {
        name: 'legalaspects-lm-entity',
        fields: [
            { name: 'sau_lm_entities.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_lm_entities.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'legalaspects-lm-entity-edit' },
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
                    routePush: { name: 'legalaspects-lm-entity-view' },
                    id: 'id',
                },
            }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/legalAspects/legalMatrix/entity/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar la entidad __name__'
            },
            }],
        }],
        configuration: {
            urlData: '/legalAspects/legalMatrix/entity/data',
            filterColumns: true,
        }
    },
    {
        name: 'legalaspects-lm-laws',
        fields: [
            { name: 'sau_lm_laws.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_lm_laws.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws_types.name', data: 'law_type', title: 'Tipo Norma', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.law_number', data: 'law_number', title: 'Número', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.law_year', data: 'law_year', title: 'Año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_risks_aspects.name', data: 'risk_aspect', title: 'Riesgo/Aspecto Ambiental', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_sst_risks.name', data: 'sst_risk', title: 'Riesgo SST', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_entities.name', data: 'entity', title: 'Ente', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.apply_system', data: 'apply_system', title: 'Sistema Aplica', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.repealed', data: 'repealed', title: 'Derogada', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'legalaspects-lm-law-edit' },
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
                    routePush: { name: 'legalaspects-lm-law-view' },
                    id: 'id',
                },
            }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/legalAspects/legalMatrix/law/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar la norma __name__'
            },
            }],
        }],
        configuration: {
            urlData: '/legalAspects/legalMatrix/law/data',
            filterColumns: true,
            configNameFilter: 'legalAspects-legalMatrix-law-crud'
        }
    },
    {
        name: 'legalaspects-lm-laws-qualify',
        fields: [
            { name: 'sau_lm_laws.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_lm_laws.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws_types.name', data: 'law_type', title: 'Tipo Norma', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.law_number', data: 'law_number', title: 'Número', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.law_year', data: 'law_year', title: 'Año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_risks_aspects.name', data: 'risk_aspect', title: 'Riesgo/Aspecto Ambiental', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_sst_risks.name', data: 'sst_risk', title: 'Riesgo SST', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_entities.name', data: 'entity', title: 'Ente', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.apply_system', data: 'apply_system', title: 'Sistema Aplica', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_lm_laws.repealed', data: 'repealed', title: 'Derogada', sortable: true, searchable: true, detail: false, key: false },
            { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
        ],
        'controlls': [{
            type: 'push',
            buttons: [{
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-clipboard',
                    title: 'Calificar'
                },
                data: {
                    routePush: { name: 'legalaspects-lm-law-qualify-view' },
                    id: 'id',
                },
            }]
        },
        {
            type: 'base',
            buttons: [],
        }],
        configuration: {
            urlData: '/legalAspects/legalMatrix/law/data',
            filterColumns: true,
            configNameFilter: 'legalAspects-legalMatrix-law-qualify'
        }
    }
]