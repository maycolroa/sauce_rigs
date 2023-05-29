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
                    routePush: { name: 'industrialsecure-dangermatrix-report-detail' },
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
    name: 'industrialsecure-dangermatrix-report-history',
    fields: [
        { name: 'sau_dm_report_histories.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                    routePush: { name: 'industrialsecure-dangermatrix-report-detail-history' },
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
        urlData: '/industrialSecurity/dangersMatrix/history/reportDangerTable',
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
    name: 'dangerousconditions-inspections-request-firm',
    fields: [
        { name: 'sau_ph_inspection_items_qualification_area_location.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                routePush: { name: 'dangerousconditions-inspections-request-firm-view' },
                id: 'id',
            },
            permission: 'ph_inspections_r'
          }]
        },
        {
            type: 'base',
            buttons: [],
        }
    ],
    configuration: {
        urlData: '/industrialSecurity/dangerousConditions/inspection/requestFirm/data',
        filterColumns: true
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
/*{
    name: 'dangerousconditions-report',
    fields: [
        { name: 'sau_ph_reports.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_ph_reports.id', data: 'id', title: 'ID', sortable: false, searchable: true, detail: false, key: false },
        { name: 'sau_employees_regionals.name', data: 'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_users.name', data: 'user', title: 'Usuario', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_conditions.description', data: 'condition', title: 'Hallazgo', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_conditions_types.description', data: 'type', title: 'Tipo de reporte', sortable: true, searchable: true, detail: false, key: false },
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
},*/
{
    name: 'dangerousconditions-report',
    fields: [
        { name: 'sau_ph_reports.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        /*{ name: 'sau_employees_regionals.name', data: 'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_users.name', data: 'user', title: 'Usuario', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_conditions.description', data: 'condition', title: 'Hallazgo', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_conditions_types.description', data: 'type', title: 'Tipo de reporte', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_reports.rate', data: 'rate', title: 'Severidad', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_ph_reports.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },*/
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
{
    name: 'industrialsecure-riskmatrix',
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
            routePush: { name: 'industrialsecure-riskmatrix-edit' },
            id: 'id',
        },
        permission: 'riskMatrix_u'
        },/* {
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-ios-copy',
            title: 'Clonar'
        },
        data: {
            routePush: { name: 'industrialsecure-riskmatrix-clone' },
            id: 'id',
        },
        permission: 'riskMatrix_c'
        },*/ {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-riskmatrix-view' },
            id: 'id',
        },
        permission: 'riskMatrix_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/risksMatrix/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la matriz de riesgos __name__'
        },
        permission: 'riskMatrix_d'
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
                action: '/industrialSecurity/risksMatrix/download/',
                id: 'id'
            },
            permission: 'riskMatrix_c'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/risksMatrix/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-subprocess',
    fields: [
        { name: 'sau_rm_sub_processes.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rm_sub_processes.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-subprocesses-edit' },
            id: 'id',
        },
        permission: 'subProcesses_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-subprocesses-view' },
            id: 'id',
        },
        permission: 'subProcesses_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/subProcess/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el subproceso __name__'
        },
        permission: 'subProcesses_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/subProcess/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-risk',
    fields: [
        { name: 'sau_rm_risk.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rm_risk.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-risks-edit' },
            id: 'id',
        },
        permission: 'risks_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-risks-view' },
            id: 'id',
        },
        permission: 'risks_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/risk/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el riesgo __name__'
        },
        permission: 'risks_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/risk/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-riskmatrix-macroprocesses',
    fields: [
        { name: 'sau_tags_processes.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_tags_processes.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_tags_processes.abbreviation', data: 'abbreviation', title: 'Abreviatura', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'industrialsecure-riskmatrix-macroprocesses-edit' },
                    id: 'id',
                },
                permission: 'riskMatrix_u'
                }, {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'industrialsecure-riskmatrix-macroprocesses-view' },
                    id: 'id',
                },
                permission: 'riskMatrix_r'
                }]
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/risksMatrix/macroprocess/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-riskmatrix-report',
    fields: [
        { name: 'sau_rm_risks_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                    routePush: { name: 'industrialsecure-riskmatrix-view' },
                    id: 'id',
                },
                permission: 'riskMatrix_r'
            }]
        },
        {
            type: 'base',
            buttons: [],
        }
    ],
    configuration: {
        urlData: '/industrialSecurity/risksMatrix/reportRiskInherentTable',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-riskmatrix-report-residual',
    fields: [
        { name: 'sau_rm_risks_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                    routePush: { name: 'industrialsecure-riskmatrix-view' },
                    id: 'id',
                },
                permission: 'riskMatrix_r'
            }]
        },
        {
            type: 'base',
            buttons: [],
        }
    ],
    configuration: {
        urlData: '/industrialSecurity/risksMatrix/reportRiskResidualTable',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epps-elements',
    fields: [
        { name: 'sau_epp_elements.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_epp_elements.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.code', data: 'code', title: 'Código', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.class_element', data: 'class_element', title: 'Clase', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.type', data: 'type', title: 'Tipo', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.mark', data: 'mark', title: 'Marca', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.identy', data: 'identy', title: '¿Identificable?', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.state', data: 'state', title: '¿Activo?', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.reusable', data: 'reusable', title: '¿Reutilizable?', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-epps-elements-edit' },
            id: 'id',
        },
        permission: 'elements_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-elements-view' },
            id: 'id',
        },
        permission: 'elements_r'
        }, /*{
        name: 'importBalanceInitial',
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'fas fa-upload',
            title: 'Importar Saldo Inicial'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-elements-import-balance-inicial' },
            id: 'id',
        },
        permission: 'elements_u'
        }*/]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/element/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el elemento __name__'
        },
        permission: 'elements_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/element/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epps-location',
    fields: [
        { name: 'sau_epp_locations.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
            routePush: { name: 'industrialsecure-epps-locations-edit' },
            id: 'id',
        },
        permission: 'location_u'
        },  {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-locations-view' },
            id: 'id',
        },
        permission: 'location_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/location/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la ubicación __name__'
        },
        permission: 'location_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/location/data',
        filterColumns: true,
    },
},
{
    name: 'industrialsecure-epps-transactions',
    fields: [
        { name: 'sau_epp_transactions_employees.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_epp_transactions_employees.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_employees.name', data: 'employee', title: 'Empleado', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_employees_positions.name', data: 'position', title: 'Cargo', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.name', data: 'elements', title: 'Elementos', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
    ],
    'controlls': [{
        type: 'push',
        buttons: [/*{
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-create',
            title: 'Editar'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-edit' },
            id: 'id',
        },
        permission: 'transaction_u'
        }, */{
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-view' },
            id: 'id',
        },
        permission: 'transaction_r'
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
          title: 'Descargar entrega en PDF'
        },
        data: {
          action: '/industrialSecurity/epp/transaction/downloadPdf/',
          id: 'id'
        },
        permission: 'transaction_r'
        }],
  },
    {
        type: 'base',
        buttons: [/*{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/transaction/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el elemento __name__'
        },
        permission: 'transaction_d'
        }*/
        {
            name: 'switchStatus',
            config: {
                color: 'outline-danger',
                borderless: true,
                icon: 'fas fa-sync',
                title: 'Revertir Entrega'
            },
            data: {
                action: '/industrialSecurity/epp/transaction/returnDelivery/',
                id: 'id',
                messageConfirmation: '¿Esta seguro de querer revertir esta entrega?'
            },
            permission: 'transaction_c'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/transaction/data',
        filterColumns: true,
        configNameFilter: 'industrialsecure-epp-delivery'
    }
  },
  {
    name: 'industrialsecure-epps-transactions-returns',
    fields: [
        { name: 'sau_epp_transactions_employees.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_epp_transactions_employees.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
        { name: 'employee', data: 'employee', title: 'Empleado', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_employees_positions.name', data: 'position', title: 'Cargo', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.name', data: 'elements', title: 'Elementos', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
    ],
    'controlls': [{
        type: 'push',
        buttons: [/*{
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-create',
            title: 'Editar'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-returns-edit' },
            id: 'id',
        },
        permission: 'transaction_u'
        },*/ {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-returns-view' },
            id: 'id',
        },
        permission: 'transaction_r'
        }]
    },
    {
        type: 'base',
        buttons: [/*{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/transaction/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el elemento __name__'
        },
        permission: 'transaction_d'
        }*/],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/transactionReturns/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epps-transactions-wastes-history',
    fields: [
        { name: 'sau_epp_wastes.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_epp_elements.name', data: 'name_element', title: 'Elemento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_locations.name', data: 'name_location', title: 'Ubicación', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_wastes.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
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
        urlData: '/industrialSecurity/epp/transaction/wastes/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epps-transactions-income',
    fields: [
        { name: 'sau_epp_incomen.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_epp_elements.name', data: 'elements', title: 'Elementos', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_locations.name', data: 'name_location', title: 'Ubicación', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_incomen.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
    ],
    'controlls': [{
        type: 'push',
        buttons: [/*{
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-create',
            title: 'Editar'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-income-edit' },
            id: 'id',
        },
        permission: 'transaction_u'
        }, */{
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-income-view' },
            id: 'id',
        },
        permission: 'transaction_r'
        }]
    },
    {
        type: 'base',
        buttons: [/*{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/transaction/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el elemento __name__'
        },
        permission: 'transaction_d'
        }*/],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/income/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epps-transactions-exit',
    fields: [
        { name: 'sau_epp_exits.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_epp_elements.name', data: 'elements', title: 'Elementos', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_locations.name', data: 'name_location', title: 'Ubicación', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_exits.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
    ],
    'controlls': [{
        type: 'push',
        buttons: [/*{
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-create',
            title: 'Editar'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-exit-edit' },
            id: 'id',
        },
        permission: 'transaction_u'
        },*/ {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-exit-view' },
            id: 'id',
        },
        permission: 'transaction_r'
        }]
    },
    {
        type: 'base',
        buttons: [/*{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/transaction/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el elemento __name__'
        },
        permission: 'transaction_d'
        }*/],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/exit/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epp-reports',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'element', data: 'element', title: 'Elemento', sortable: true, searchable: false, detail: false, key: false },
        { name: 'class', data: 'class', title: 'Clase', sortable: true, searchable: false, detail: false, key: false },
        { name: 'mark', data: 'mark', title: 'Marca', sortable: true, searchable: false, detail: false, key: false },
        { name: 'location', data: 'location', title: 'Ubicación', sortable: true, searchable: false, detail: false, key: false },
        { name: 'quantity', data: 'quantity', title: 'Cantidad Total', sortable: true, searchable: false, detail: false, key: false },
        { name: 'quantity_available', data: 'quantity_available', title: 'Cantidad Disponible', sortable: true, searchable: false, detail: false, key: false },
        { name: 'quantity_allocated', data: 'quantity_allocated', title: 'Cantidad Asignada', sortable: true, searchable: false, detail: false, key: false },
        { name: 'quantity_transfer', data: 'quantity_transfer', title: 'Cantidad en transito', sortable: true, searchable: false, detail: false, key: false },
        { name: 'quantity_wastes', data: 'quantity_wastes', title: 'Cantidad desechada', sortable: true, searchable: false, detail: false, key: false },
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
        urlData: '/industrialSecurity/epp/element/reportBalance',
        filterColumns: false,
        //configNameFilter: 'industrialsecure-epp-report'
    }
},
{
    name: 'industrialsecure-epps-transactions-transfers-location',
    fields: [
        { name: 'sau_epp_transfers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'name_location_origin', data: 'name_location_origin', title: 'Ubicación origen', sortable: true, searchable: true, detail: false, key: false },
        { name: 'name_location_destiny', data: 'name_location_destiny', title: 'Ubicación destino', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.name', data: 'elements', title: 'Elementos', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_transfers.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
    ],
    'controlls': [{
        type: 'push',
        buttons: [/*{
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-create',
            title: 'Editar'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-exit-edit' },
            id: 'id',
        },
        permission: 'transaction_u'
        },*/ {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-transfers-location-view' },
            id: 'id',
        },
        permission: 'transaction_r'
        }]
    },
    {
        type: 'base',
        buttons: [/*{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/transaction/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el elemento __name__'
        },
        permission: 'transaction_d'
        }*/],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/transfer/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epps-transactions-reception',
    fields: [
        { name: 'sau_epp_receptions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'name_location_origin', data: 'name_location_origin', title: 'Ubicación origen', sortable: true, searchable: true, detail: false, key: false },
        { name: 'name_location_destiny', data: 'name_location_destiny', title: 'Ubicación destino', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.name', data: 'elements', title: 'Elementos', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_receptions.state', data: 'state', title: 'Estado', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_receptions.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-epps-transactions-reception-edit' },
            id: 'id',
        },
        permission: 'transaction_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-epps-transactions-reception-view' },
            id: 'id',
        },
        permission: 'transaction_r'
        }]
    },
    {
        type: 'base',
        buttons: [/*{
        name: 'delete',
        data: {
            action: '/industrialSecurity/epp/transaction/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el elemento __name__'
        },
        permission: 'transaction_d'
        }*/],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/reception/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epp-reports-employees',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'employee', data: 'employee', title: 'Empleado', sortable: true, searchable: false, detail: false, key: false },
        { name: 'element', data: 'element', title: 'Elemento', sortable: true, searchable: false, detail: false, key: false },
        { name: 'class', data: 'class', title: 'Clase', sortable: true, searchable: false, detail: false, key: false },
        { name: 'location', data: 'location', title: 'Ubicación', sortable: true, searchable: false, detail: false, key: false },
        { name: 'cantidad', data: 'cantidad', title: 'Cantidad', sortable: true, searchable: false, detail: false, key: false },
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
        urlData: '/industrialSecurity/epp/element/reportEmployee',
        filterColumns: false,
        //configNameFilter: 'industrialsecure-epp-report'
    }
},
{
    name: 'industrialsecure-documents',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'name', data: 'name', title: 'Nombre', sortable: true, searchable: false, detail: false, key: false },
        { name: 'user_name', data: 'user_name', title: 'Usuario creador', sortable: true, searchable: false, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-documentssecurity-edit' },
            id: 'id',
        },
        //permission: 'documentsSecurity_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-documentssecurity-view' },
            id: 'id',
        },
        //permission: 'documentsSecurity_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/document/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el documento __name__'
        },
        //permission: 'documentsSecurity_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/document/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-accidents',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'nombre_persona', data: 'nombre_persona', title: 'Empleado', sortable: true, searchable: false, detail: false, key: false },
        { name: 'identificacion_persona', data: 'identificacion_persona', title: 'Identificación', sortable: true, searchable: false, detail: false, key: false },
        { name: 'fecha_accidente', data: 'fecha_accidente', title: 'Fecha de accidente', sortable: true, searchable: false, detail: false, key: false },
        { name: 'consolidado', data: 'consolidado', title: 'Estado', sortable: true, searchable: false, detail: false, key: false },
        { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
    ],
    'controlls': [
        {
        type: 'push',
        buttons: [
            {
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-create',
                    title: 'Editar'
                },
                data: {
                    routePush: { name: 'industrialsecure-accidentswork-edit' },
                    id: 'id',
                },
                permission: 'accidentsWork_u'
            }, 
            {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'industrialsecure-accidentswork-view' },
                    id: 'id',
                },
                permission: 'accidentsWork_r'
            },
            {
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-list',
                    title: 'Causas'
                },
                data: {
                    routePush: { name: 'industrialsecure-accidentswork-causes' },
                    id: 'id',
                },
                permission: 'accidentsWork_u'
            }
        ]
    },
    {
        type: 'simpleDownload',
        buttons: [{
        name: 'downloadFile',
        config: {
          color: 'outline-danger',
          borderless: true,
          icon: 'fas fa-file-pdf',
          title: 'Descargar formulario en PDF'
        },
        data: {
          action: '/industrialSecurity/accidents/downloadPdf/',
          id: 'id'
        },
        permission: 'accidentsWork_r'
        }],
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/accidents/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el formulario de accidente __name__'
        },
        permission: 'accidentsWork_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/accidents/data',
        configNameFilter: 'industrialsecure-accidents',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-epp-reports-stock-minimun',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'element', data: 'element', title: 'Elemento', sortable: true, searchable: false, detail: false, key: false },
        { name: 'location', data: 'location', title: 'Ubicación', sortable: true, searchable: false, detail: false, key: false },
        { name: 'quantity', data: 'quantity', title: 'Existencia Mínima', sortable: true, searchable: false, detail: false, key: false },
        { name: 'quantity_available', data: 'quantity_available', title: 'Existencia Disponible', sortable: true, searchable: false, detail: false, key: false },
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
        urlData: '/industrialSecurity/epp/element/reportStockMinimun',
        filterColumns: false,
        //configNameFilter: 'industrialsecure-epp-report'
    }
},
];
