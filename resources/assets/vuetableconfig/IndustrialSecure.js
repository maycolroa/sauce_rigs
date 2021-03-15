export default [
{
    name: 'industrialsecure-activities',
    fields: [
        { name: 'sau_dm_activities.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dm_activities.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-activities-edit' },
            id: 'id',
        },
        permission: 'activities_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-activities-view' },
            id: 'id',
        },
        permission: 'activities_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/activity/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la actividad __name__'
        },
        permission: 'activities_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/activity/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangers',
    fields: [
        { name: 'sau_dm_dangers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dm_dangers.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-dangers-edit' },
            id: 'id',
        },
        permission: 'dangers_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-dangers-view' },
            id: 'id',
        },
        permission: 'dangers_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/danger/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el peligro __name__'
        },
        permission: 'dangers_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/danger/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix',
    fields: [{ name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }],
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
            routePush: { name: 'industrialsecure-dangermatrix-edit' },
            id: 'id',
        },
        permission: 'dangerMatrix_u'
        }, {
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-ios-copy',
            title: 'Clonar'
        },
        data: {
            routePush: { name: 'industrialsecure-dangermatrix-clone' },
            id: 'id',
        },
        permission: 'dangerMatrix_c'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-dangermatrix-view' },
            id: 'id',
        },
        permission: 'dangerMatrix_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/dangersMatrix/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la matriz de peligro __name__'
        },
        permission: 'dangerMatrix_d'
        }],
    },
    {
        type: 'download',
        buttons: [{
            name: 'downloadMatrix',
            config: {
                color: 'outline-success',
                borderless: true,
                icon: 'ion ion-md-cloud-download',
                title: 'Exportar'
            },
            data: {
                action: '/industrialSecurity/dangersMatrix/download/',
                id: 'id'
            },
            permission: 'dangerMatrix_export'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-history',
    fields: [
        { name: 'sau_dm_change_histories.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dm_change_histories.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_users.name', data: 'name', title: 'Responsable', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_dm_change_histories.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
    ],
    'controlls': [],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrixHistory/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-report',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [{
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'industrialsecure-dangermatrix-view' },
                    id: 'id',
                },
                permission: 'dangerMatrix_r'
            }]
        },
        {
            type: 'base',
            buttons: [],
        }
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/reportDangerTable',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-administrative-controls',
    fields: [
        { name: 'sau_tags_administrative_controls.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_administrative_controls.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/administrativeControls/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/administrativeControls/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-engineering-controls',
    fields: [
        { name: 'sau_tags_engineering_controls.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_engineering_controls.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/engineeringControls/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/engineeringControls/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-possible-consequences-danger',
    fields: [
        { name: 'sau_tags_possible_consequences_danger.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_possible_consequences_danger.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/possibleConsequencesDanger/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/possibleConsequencesDanger/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-substitutions',
    fields: [
        { name: 'sau_tags_substitutions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_substitutions.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/substitution/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/substitution/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-warning-signage',
    fields: [
        { name: 'sau_tags_warning_signage.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_warning_signage.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/warningSignage/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/warningSignage/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-epp',
    fields: [
        { name: 'sau_tags_epp.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_epp.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/epp/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/epp/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-participants',
    fields: [
        { name: 'sau_tags_participantsx.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_participants.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/participants/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/participants/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix-tags-danger-description',
    fields: [
        { name: 'sau_tags_danger_description.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_danger_description.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/dangerDescription/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/tags/dangerDescription/data',
        filterColumns: true,
    }
},
/*{
    name: 'inspections-conditionsReports',
    fields: [
        { name: 'sau_inspect_conditions_reports.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_employees_regionals.name', data: 'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_users.name', data: 'user_name', title: 'Usuario', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_inspect_conditions.description', data: 'condition', title: 'Condicion', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_inspect_conditions_type.description', data: 'type', title: 'Tipo de condicion', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_inspect_conditions_reports.rate', data: 'rate', title: 'Severidad', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_inspect_conditions_reports.created_at', data: 'created_at', title: 'Fecha de creacion', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [{
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-create',
                    title: 'Editar'
                },
                data: {
                    routePush: { name: 'inspections-conditionsReports-edit' },
                    id: 'id',
                },
                permission: 'inspect_conditionsReports_u'
                },
                {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'inspections-conditionsReports-view' },
                    id: 'id',
                },
                permission: 'inspect_conditionsReports_r'
            }],
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/inspections/conditionsReports/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el reporte'
            },
            permission: 'inspect_conditionsReports_d'
            }],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/inspections/conditionsReports/data',
        filterColumns: true,
    }
},*/
{
    name: 'dangerousconditions-inspections',
    fields: [
        { name: 'sau_ph_inspections.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                routePush: { name: 'dangerousconditions-inspections-edit' },
                id: 'id',
            },
            permission: 'ph_inspections_u'
          }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'dangerousconditions-inspections-view' },
                id: 'id',
            },
            permission: 'ph_inspections_r'
          }, {
            config: {
                color: 'outline-success',
                borderless: true,
                icon: 'ion ion-ios-copy',
                title: 'Clonar'
            },
            data: {
                routePush: { name: 'dangerousconditions-inspections-clone' },
                id: 'id',
            },
            permission: 'ph_inspections_c'
          },{
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-list',
                title: 'Calificadas'
            },
            data: {
                routePush: { name: 'dangerousconditions-inspections-qualification' },
                id: 'id',
            },
            permission: 'ph_inspections_r'
        }]
      },
      {
        type: 'base',
        buttons: [
            {
                name: 'switchStatus',
                config: {
                    color: 'outline-danger',
                    borderless: true,
                    icon: 'fas fa-sync',
                    title: 'Cambiar Estado'
                },
                data: {
                    action: '/industrialSecurity/dangerousConditions/inspection/switchStatus/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                },
                permission: 'ph_inspections_u'
            },
            {
                name: 'delete',
                data: {
                    action: '/industrialSecurity/dangerousConditions/inspection/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar la inspección planeada'
                },
                permission: 'ph_inspections_d'
                }
        ],
      }],
    configuration: {
        urlData: '/industrialSecurity/dangerousConditions/inspection/data',
        filterColumns: true,
        configNameFilter: 'dangerousconditions-inspections'
    }
},
{
    name: 'dangerousconditions-inspections-qualification',
    fields: [
        { name: 'sau_ph_inspection_items_qualification_area_location.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                routePush: { name: 'dangerousconditions-inspections-qualification-view' },
                id: 'id',
            },
            permission: 'ph_inspections_r'
          }]
      },
      {
            type: 'simpleDownload',
            buttons: [{
            name: 'downloadFile',
            config: {
              color: 'outline-danger',
              borderless: true,
              icon: 'fas fa-file-pdf',
              title: 'Descargar inspección planeada en PDF'
            },
            data: {
              action: '/industrialSecurity/dangerousConditions/inspection/downloadPdf/',
              id: 'id'
            },
            permission: 'ph_inspections_r'
            }],
      },
      {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/dangerousConditions/inspection/qualification/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la inspección planeada realizada'
        },
        permission: 'ph_qualification_inspection_d'
        }],
      }],
    configuration: {
        urlData: '/industrialSecurity/dangerousConditions/inspection/qualification/data',
        filterColumns: true,
        configNameFilter: 'dangerousconditions-inspections-qualification'
    }
},
{
    name: 'dangerousconditions-inspections-report',
    fields: [
        { name: 'sau_ph_inspection_items_qualification_area_location.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
        urlData: '/industrialSecurity/dangerousConditions/inspection/report',
        filterColumns: false,
        configNameFilter: 'dangerousconditions-inspections-report'
    }
},
{
    name: 'dangerousconditions-inspections-report-type-2',
    fields: [
        { name: 'sau_ph_inspection_items_qualification_area_location.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
        urlData: '/industrialSecurity/dangerousConditions/inspection/reportType2',
        filterColumns: false,
        //configNameFilter: 'dangerousconditions-inspections-report'
    }
},
{
    name: 'dangerousconditions-report',
    fields: [
        { name: 'sau_ph_reports.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_ph_reports.id', data: 'id', title: 'ID', sortable: false, searchable: true, detail: false, key: false },
        { name: 'sau_employees_regionals.name', data: 'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_users.name', data: 'user', title: 'Usuario', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_conditions.description', data: 'condition', title: 'Condición', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_conditions_types.description', data: 'type', title: 'Tipo de condición', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_reports.rate', data: 'rate', title: 'Severidad', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_reports.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'dangerousconditions-reports-edit' },
                id: 'id',
            },
            permission: 'ph_reports_u'
          }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'dangerousconditions-reports-view' },
                id: 'id',
            },
            permission: 'ph_reports_r'
          }]
      },
      {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/dangerousConditions/report/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la inspección no planeada'
        },
        permission: 'ph_reports_r'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/dangerousConditions/report/data',
        filterColumns: true,
        configNameFilter: 'dangerousconditions-report'
    }
},
];
