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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-administrative-controls-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-administrative-controls-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [/*{
            name: 'delete',
            data: {
                action: '/industrialSecurity/tags/administrativeControls/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar el tag __name__'
            },
            permission: 'dangerMatrix_c'
            }*/],
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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-engineering-controls-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-engineering-controls-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [],
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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-possible-consequences-danger-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-possible-consequences-danger-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [],
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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-substitutions-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-substitutions-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [],
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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-warning-signage-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-warning-signage-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [],
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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-epp-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-epp-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [],
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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-participants-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-participants-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [],
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
            buttons: [
                {
                    config: {
                        color: 'outline-success',
                        borderless: true,
                        icon: 'ion ion-md-create',
                        title: 'Editar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-danger-description-edit' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
                {
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'fas fa-sync',
                        title: 'Eliminar/Reemplazar'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-tags-danger-description-deleted' },
                        id: 'id',
                    },
                    permission: 'dangerMatrix_c'
                },
            ],
        },
        {
            type: 'base',
            buttons: [],
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
    name: 'dangerousconditions-inspections-report-gestion',
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
        urlData: '/industrialSecurity/dangerousConditions/inspection/reportGestion',
        filterColumns: true,
        configNameFilter: 'dangerousconditions-inspections-report-gestion'
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
        //{ name: 'sau_epp_elements.identy', data: 'identy', title: '¿Identificable?', sortable: true, searchable: true, detail: false, key: false },
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
        { name: 'sau_epp_elements.name', data: 'element', title: 'Elemento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.class_element', data: 'class', title: 'Clase', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.mark', data: 'mark', title: 'Marca', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_locations.name', data: 'location', title: 'Ubicación', sortable: true, searchable: true, detail: false, key: false },
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
        filterColumns: true,
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
        { name: 'sau_employees.name', data: 'employee', title: 'Empleado', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.name', data: 'element', title: 'Elemento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.class_element', data: 'class', title: 'Clase', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_locations.name', data: 'location', title: 'Ubicación', sortable: true, searchable: true, detail: false, key: false },
        { name: 'cantidad', data: 'cantidad', title: 'Cantidad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_epp_transactions_employees.created_at', data: 'fecha', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
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
        filterColumns: true,
        //configNameFilter: 'industrialsecure-epp-report'
    }
},
{
    name: 'industrialsecure-epp-reports-employees-history',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_employees.name', data: 'employee', title: 'Empleado', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.name', data: 'element', title: 'Elemento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_elements.class_element', data: 'class', title: 'Clase', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_locations.name', data: 'location', title: 'Ubicación', sortable: true, searchable: true, detail: false, key: false },
        { name: 'cantidad', data: 'cantidad', title: 'Cantidad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_epp_transactions_employees.created_at', data: 'fecha', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
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
        urlData: '/industrialSecurity/epp/element/reportEmployeeHistory',
        filterColumns: true,
        //configNameFilter: 'industrialsecure-epp-report'
    }
},
{
    name: 'industrialsecure-documents',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_documents_security.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_documents_security.updated_at', data: 'updated_at', title: 'Fecha de carga', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_users.name', data: 'user_name', title: 'Usuario creador', sortable: true, searchable: true, detail: false, key: false },
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
        { name: 'nombre_persona', data: 'nombre_persona', title: 'Persona asociada al evento', sortable: true, searchable: false, detail: false, key: false },
        { name: 'identificacion_persona', data: 'identificacion_persona', title: 'Identificación', sortable: true, searchable: false, detail: false, key: false },
        { name: 'fecha_accidente', data: 'fecha_accidente', title: 'Fecha de evento', sortable: true, searchable: false, detail: false, key: false },
        { name: 'estado_evento', data: 'estado_evento', title: 'Estado', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_users.name', data: 'user', title: 'Reportado por', sortable: true, searchable: false, detail: false, key: false },
        { name: 'consolidado', data: 'consolidado', title: '¿Abierto?', sortable: true, searchable: false, detail: false, key: false },
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
                    title: 'Investigacion'
                },
                data: {
                    routePush: { name: 'industrialsecure-accidentswork-investigation' },
                    id: 'id',
                },
                permission: 'accidentsWork_investigation'
            },
            /*{
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-list',
                    title: 'Analisis de causas'
                },
                data: {
                    routePush: { name: 'industrialsecure-accidentswork-causes' },
                    id: 'id',
                },
                permission: 'accidentsWork_investigation'
            },*/
            {
                name: 'pdfSendMail',
                config: {
                    color: 'outline-danger',
                    borderless: true,
                    icon: 'ion ion-ios-mail',
                    title: 'Enviar Reporte al correo'
                },
                data: {
                    routePush: { name: 'industrialsecure-accidentswork-send-pdf' },
                    id: 'id',
                },
                permission: 'accidentsWork_r'
            },
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
        buttons: [
            {
                name: 'delete',
                data: {
                    action: '/industrialSecurity/accidents/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar el formulario de accidente __name__'
                },
                permission: 'accidentsWork_d'
                },
            {
                name: 'switchStatus',
                config: {
                    color: 'outline-danger',
                    borderless: true,
                    icon: 'fas fa-sync',
                    title: 'Cambiar Estado'
                },
                data: {
                    action: '/industrialSecurity/accidents/switchStatus/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                },
                permission: 'employees_u'
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
        { name: 'sau_epp_elements.name', data: 'element', title: 'Elemento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_epp_locations.name', data: 'location', title: 'Ubicación', sortable: true, searchable: true, detail: false, key: false },
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
        filterColumns: true,
        //configNameFilter: 'industrialsecure-epp-report'
    }
},
{
    name: 'industrialsecure-dangermatrix-log-qualifications',
    fields: [
        { name: 'sau_dm_history_qualification_change.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: true, detail: false, key: false },
        { name: 'fecha', data: 'fecha', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
        { name: 'year', data: 'year', title: 'Año', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_users.name', data: 'user', title: 'Usuario', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: true, detail: false, key: false },
        { name: 'nr_persona_old', data: 'nr_persona_old', title: 'NR Persona Anterior', sortable: true, searchable: false, detail: false, key: false },
        { name: 'nr_persona_new', data: 'nr_persona_new', title: 'NR Persona Nuevo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'qualification_old2', data: 'qualification_old2', title: 'Calificación Anterior', sortable: true, searchable: false, detail: false, key: false },
        { name: 'qualification_new2', data: 'qualification_new2', title: 'Calificación Nueva', sortable: true, searchable: false, detail: false, key: false },
        { name: 'observation_old2', data: 'observation_old2', title: 'Observaciones Anteriores', sortable: true, searchable: false, detail: false, key: false },
        { name: 'observation_new2', data: 'observation_new2', title: 'Observaciones Nueva', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/historyQualification/data',
        filterColumns: true,
        configNameFilter: 'industrialsecure-dangermatrix-log-qualification'
    }
},
{
    name: 'industrialsecure-dangermatrix-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-administrative-control-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/tagAdministrativeControls/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-engineering-control-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/engineeringControls/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-epp-control-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/epp/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-warning-signage-control-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/warningSignage/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-substitutions-control-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/substitution/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-possible-consequences-danger-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/possibleConsequencesDanger/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-participants-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/participants/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-tags-danger-description-search',
    fields: [
        { name: 'sau_dangers_matrix.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_dangers_matrix.name', data: 'matriz', title: 'Matriz', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_activities.name', data: 'activity', title: 'Actividad', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_dm_dangers.name', data: 'danger', title: 'Peligro', sortable: true, searchable: false, detail: false, key: false },
        { name: 'campo', data: 'campo', title: 'Campo', sortable: true, searchable: false, detail: false, key: false },
        { name: 'value', data: 'value', title: 'Valor', sortable: true, searchable: false, detail: false, key: false }
    ],
    'controlls': [
        {
            type: 'push',
            buttons: [],
        },
        {
            type: 'base',
            buttons: [],
        },
    ],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/dangerDescription/searchKeyword/data',
        filterColumns: true,
        /*configNameFilter: 'industrialsecure-dangermatrix-log-qualification'*/
    }
},
{
    name: 'industrialsecure-accidents-causes-categories',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'category_name', data: 'category_name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_aw_causes_sections.section_name', data: 'section_name', title: 'Sección', sortable: true, searchable: true, detail: false, key: false },
        { name: 'causes_name', data: 'causes_name', title: 'Causa', sortable: false, searchable: false, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-accidentswork-causes-categories-edit' },
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
            routePush: { name: 'industrialsecure-accidentswork-causes-categories-view' },
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
            action: '/industrialSecurity/causes/categories/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la categoria __category_name__'
        },
        //permission: 'documentsSecurity_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/causes/categories/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-accidents-causes-items',
    fields: [
        { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'item_name', data: 'item_name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_aw_causes_section_category_company.category_name', data: 'category_name', title: 'Categoria', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_aw_causes_sections.section_name', data: 'section_name', title: 'Sección', sortable: true, searchable: true, detail: false, key: false },
        { name: 'causes_name', data: 'causes_name', title: 'Causa', sortable: false, searchable: false, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-accidentswork-causes-items-edit' },
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
            routePush: { name: 'industrialsecure-accidentswork-causes-items-view' },
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
            action: '/industrialSecurity/causes/items/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el item __item_name__'
        },
        //permission: 'documentsSecurity_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/causes/items/data',
        filterColumns: true,
    }
},
{
    name: 'inspections-helpers',
    fields: [
        { name: 'sau_helpers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_helpers.title', data: 'title', title: 'Título', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
        //{ name: 'sau_modules.display_name', data: 'module', title: 'Modulo', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'dangerousconditions-inspections-customHelpers-view' },
              id: 'id'
          },
          permission: ''
        }]
    },
    {
        type: 'base',
        buttons: [],
    }],
    configuration: {
        urlData: '/industrialSecurity/dangerousConditions/helpers/data',
        filterColumns: true,
    }
},
{
    name: 'dangermatrix-helpers',
    fields: [
        { name: 'sau_helpers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_helpers.title', data: 'title', title: 'Título', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
        //{ name: 'sau_modules.display_name', data: 'module', title: 'Modulo', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'dangerMatrix-customHelpers-view' },
              id: 'id'
          },
          permission: ''
        }]
    },
    {
        type: 'base',
        buttons: [],
    }],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/helpers/data',
        filterColumns: true,
    }
},
{
    name: 'epp-helpers',
    fields: [
        { name: 'sau_helpers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_helpers.title', data: 'title', title: 'Título', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
        //{ name: 'sau_modules.display_name', data: 'module', title: 'Modulo', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'epp-customHelpers-view' },
              id: 'id'
          },
          permission: ''
        }]
    },
    {
        type: 'base',
        buttons: [],
    }],
    configuration: {
        urlData: '/industrialSecurity/epp/helpers/data',
        filterColumns: true,
    }
},
{
    name: 'riskMatrix-helpers',
    fields: [
        { name: 'sau_helpers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_helpers.title', data: 'title', title: 'Título', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
        //{ name: 'sau_modules.display_name', data: 'module', title: 'Modulo', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'riskMatrix-customHelpers-view' },
              id: 'id'
          },
          permission: ''
        }]
    },
    {
        type: 'base',
        buttons: [],
    }],
    configuration: {
        urlData: '/industrialSecurity/risksMatrix/helpers/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-roadsafety-documents',
    fields: [
        { name: 'sau_rs_positions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_positions.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-roadsafety-documents-edit' },
            id: 'id',
        },
        permission: 'roadsafety_documents_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-roadsafety-documents-view' },
            id: 'id',
        },
        permission: 'roadsafety_documents_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/roadsafety/documents/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el documento __name__'
        },
        permission: 'roadsafety_documents_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/documents/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-roadsafety-vehicles',
    fields: [
        { name: 'sau_rs_vehicles.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
    ],
    'controlls': [{
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
                    routePush: { name: 'industrialsecure-roadsafety-vehicles-edit' },
                    id: 'id',
                },
                permission: 'roadsafety_vehicles_u'
            }, {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'industrialsecure-roadsafety-vehicles-view' },
                    id: 'id',
                },
                permission: 'roadsafety_vehicles_r'
            },
            {
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-clipboard',
                    title: 'Realizar Mantenimiento'
                },
                data: {
                    routePush: { name: 'industrialsecure-roadsafety-vehicles-maintenance-create' },
                    id: 'id',
                },
                permission: 'roadsafety_vehicles_c'
            },
            {
                config: {
                        color: 'outline-info',
                        borderless: true,
                        icon: 'ion ion-md-list',
                        title: 'Ver Mantenimientos Realizados'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-roadsafety-vehicles-maintenance' },
                        id: 'id',
                    },
                    permission: 'roadsafety_vehicles_r'
            },
            {
                config: {
                    color: 'outline-warning',
                    borderless: true,
                    icon: 'ion ion-md-clipboard',
                    title: 'Registrar carga de combustible'
                },
                data: {
                    routePush: { name: 'industrialsecure-roadsafety-vehicles-combustible-create' },
                    id: 'id',
                },
                permission: 'roadsafety_vehicles_c'
            },
            {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-list',
                    title: 'Ver Cargas de combustible Realizados'
                },
                data: {
                    routePush: { name: 'industrialsecure-roadsafety-vehicles-combustible' },
                    id: 'id',
                },
                permission: 'roadsafety_vehicles_r'
            }
        ]
      },
      {
        type: 'base',
        buttons: [
            {
                name: 'delete',
                data: {
                    action: '/industrialSecurity/roadsafety/vehicles/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar el vehiculo'
                },
                permission: 'roadsafety_vehicles_d'
                }
        ],
      }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/vehicles/data',
        filterColumns: true,
        configNameFilter: 'industrialsecure-roadsafety-vehicles'
    }
},
{
    name: 'industrialsecure-roadsafety-maintenance',
    fields: [
        { name: 'sau_rs_vehicle_maintenance.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_vehicle_maintenance.date', data: 'date', title: 'Fecha de mantenimiento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_maintenance.type', data: 'type', title: 'Tipo de mantenimiento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_maintenance.km', data: 'km', title: 'Kilometraje', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_maintenance.responsible', data: 'responsible', title: 'Responsable', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_maintenance.apto', data: 'apto', title: '¿Apto para retomar marcha?', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_maintenance.next_date', data: 'next_date', title: 'Fecha de proximo mantenimiento', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-roadsafety-vehicles-maintenance-edit' },
            id: 'id',
        },
        permission: 'roadsafety_vehicles_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-roadsafety-vehicles-maintenance-view' },
            id: 'id',
        },
        permission: 'roadsafety_vehicles_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/roadsafety/vehiclesMaintenance/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el mantenimiento con fecha __date__'
        },
        permission: 'roadsafety_vehicles_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/vehiclesMaintenance/data',
        filterColumns: true,
    }
},
{
    name: 'roadsafety-helpers',
    fields: [
        { name: 'sau_helpers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_helpers.title', data: 'title', title: 'Título', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_helpers.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
        //{ name: 'sau_modules.display_name', data: 'module', title: 'Modulo', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'industrialsecure-roadsafety-customHelpers-view' },
              id: 'id'
          },
          permission: ''
        }]
    },
    {
        type: 'base',
        buttons: [],
    }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/helpers/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-roadsafety-drivers',
    fields: [
        { name: 'sau_rs_drivers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                routePush: { name: 'industrialsecure-roadsafety-drivers-edit' },
                id: 'id',
            },
            permission: 'roadsafety_drivers_u'
          }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'industrialsecure-roadsafety-drivers-view' },
                id: 'id',
            },
            permission: 'roadsafety_drivers_r'
          },
          {
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-clipboard',
                    title: 'Registrar infracción'
                },
                data: {
                    routePush: { name: 'industrialsecure-roadsafety-drivers-infraction-create' },
                    id: 'id',
                },
                permission: 'roadsafety_drivers_c'
          },
          {
                config: {
                        color: 'outline-info',
                        borderless: true,
                        icon: 'ion ion-md-list',
                        title: 'Ver infracción Registradas'
                    },
                    data: {
                        routePush: { name: 'industrialsecure-roadsafety-drivers-infraction' },
                        id: 'id',
                    },
                    permission: 'roadsafety_drivers_r'
          },]
      },
      {
        type: 'base',
        buttons: [
            {
                name: 'delete',
                data: {
                    action: '/industrialSecurity/roadsafety/drivers/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar el conductor'
                },
                permission: 'roadsafety_drivers_d'
                }
        ],
      }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/drivers/data',
        filterColumns: true,
        //configNameFilter: 'dangerousconditions-inspections'
    }
},
{
    name: 'industrialsecure-road-safety-history-soat',
    fields: [
        { name: 'sau_rs_history_records_vehicles.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_history_records_vehicles.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_users.name', data: 'user', title: 'Responsable', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_history_records_vehicles.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
    ],
    'controlls': [],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/vehicles/historySoatdata',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-road-safety-history-mechanical',
    fields: [
        { name: 'sau_rs_history_records_vehicles.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_history_records_vehicles.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_users.name', data: 'name', title: 'Responsable', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_history_records_vehicles.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
    ],
    'controlls': [],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/vehicles/historyMechanicaldata',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-road-safety-history-responsability',
    fields: [
        { name: 'sau_rs_history_records_vehicles.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_history_records_vehicles.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: false, detail: false, key: false },
        { name: 'sau_users.name', data: 'name', title: 'Responsable', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_history_records_vehicles.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
    ],
    'controlls': [],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/vehicles/historyResponsabilitydata',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-roadsafety-combustible',
    fields: [
        { name: 'sau_rs_vehicle_combustibles.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_vehicle_combustibles.date', data: 'date', title: 'Fecha de mantenimiento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_combustibles.cylinder_capacity', data: 'cylinder_capacity', title: 'Cilindraje', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_combustibles.km', data: 'km', title: 'Kilometraje', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicle_combustibles.quantity_galons', data: 'quantity_galons', title: 'Cantidad de galones', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_employees.name', data: 'driver', title: 'Conductor', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-roadsafety-vehicles-combustible-edit' },
            id: 'id',
        },
        permission: 'roadsafety_vehicles_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-roadsafety-vehicles-combustible-view' },
            id: 'id',
        },
        permission: 'roadsafety_vehicles_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/roadsafety/vehiclesCombustible/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la carga de combustible con fecha __date__'
        },
        permission: 'roadsafety_vehicles_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/vehiclesCombustible/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-roadsafety-inspections',
    fields: [
        { name: 'sau_rs_inspections.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                routePush: { name: 'roadSafety-inspections-edit' },
                id: 'id',
            },
            permission: 'roadsafety_inspections_u'
          }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'roadSafety-inspections-view' },
                id: 'id',
            },
            permission: 'roadsafety_inspections_r'
          }, {
            config: {
                color: 'outline-success',
                borderless: true,
                icon: 'ion ion-ios-copy',
                title: 'Clonar'
            },
            data: {
                routePush: { name: 'roadSafety-inspections-clone' },
                id: 'id',
            },
            permission: 'roadsafety_inspections_c'
          },{
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-list',
                title: 'Calificadas'
            },
            data: {
                routePush: { name: 'roadSafety-inspections-qualification' },
                id: 'id',
            },
            permission: 'roadsafety_inspections_r'
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
                    action: '/industrialSecurity/roadsafety/inspection/switchStatus/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                },
                permission: 'roadsafety_inspections_u'
            },
            {
                name: 'delete',
                data: {
                    action: '/industrialSecurity/roadsafety/inspection/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar la inspección planeada'
                },
                permission: 'roadsafety_inspections_d'
                }
        ],
      }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/inspections/data',
        filterColumns: true,
        configNameFilter: 'roadSafety-inspections'
    }
},
{
    name: 'roadsafety-inspections-qualification',
    fields: [
        { name: 'sau_rs_inspections_qualified.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
                routePush: { name: 'roadSafety-inspections-qualification-view' },
                id: 'id',
            },
            permission: 'roadsafety_inspections_r'
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
              title: 'Descargar inspección en PDF'
            },
            data: {
              action: '/industrialSecurity/roadsafety/inspection/downloadPdf/',
              id: 'id'
            },
            permission: 'roadsafety_inspections_r'
            }],
      },
      {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/roadsafety/inspection/qualification/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la inspección realizada'
        },
        permission: 'roadsafety_inspections_qualifications_d'
        }],
      }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/inspection/qualification/data',
        filterColumns: true,
        configNameFilter: 'roadSafety-inspections-qualification'
    }
},
{
    name: 'industrialsecure-roadsafety-infraction',
    fields: [
        { name: 'sau_rs_driver_infractions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_driver_infractions.date_simit', data: 'date_simit', title: 'Fecha de la consulta inicial en el simit', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_driver_infractions.date', data: 'date', title: 'Fecha de infraccion', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_vehicles.plate', data: 'vehicle', title: 'Placa de vehiculo', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_infractions_type.type', data: 'type', title: 'Tipo de infracción', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_infractions_type_codes.code', data: 'code', title: 'Códigos de infracción', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-roadsafety-drivers-infraction-edit' },
            id: 'id',
        },
        permission: 'roadsafety_drivers_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-roadsafety-drivers-infraction-view' },
            id: 'id',
        },
        permission: 'roadsafety_drivers_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/industrialSecurity/roadsafety/driversInfractions/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la carga de combustible con fecha __date__'
        },
        permission: 'roadsafety_drivers_d'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/driverInfractions/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-roadsafety-trainings',
    fields: [
        { name: 'sau_rs_trainings.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_rs_trainings.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_trainings.active', data: 'active', title: '¿Activa?', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_trainings.created_at', data: 'created_at', title: 'Fecha creación', sortable: true, searchable: true, detail: false, key: false },
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
            routePush: { name: 'industrialsecure-roadsafety-trainings-edit' },
            id: 'id',
        },
        permission: 'roadsafety_trainings_u'
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
            title: 'Ver'
        },
        data: {
            routePush: { name: 'industrialsecure-roadsafety-trainings-view' },
            id: 'id',
        },
        permission: 'roadsafety_trainings_r'
        },{
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-list',
                title: 'Ver Capacitaciones Realizadas'
            },
            data: {
                routePush: { name: 'industrialsecure-roadsafety-trainings-employees' },
                id: 'id',
            },
            permission: 'roadsafety_trainings_u'
        }]
    },
    {
        type: 'base',
        buttons: [{
            name: 'delete',
            data: {
                action: '/industrialSecurity/roadsafety/training/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar la capacitación __name__'
            },
            permission: 'roadsafety_trainings_d'
        },
        {
            name: 'switchStatus',
            config: {
                color: 'outline-danger',
                borderless: true,
                icon: 'fas fa-sync',
                title: 'Cambiar Estado'
            },
            data: {
                action: '/industrialSecurity/roadsafety/training/switchStatus/',
                id: 'id',
                messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
            },
            permission: 'roadsafety_trainings_u'
        },
        {
            name: 'retrySendMail',
            config: {
                color: 'outline-danger',
                borderless: true,
                icon: 'ion ion-ios-mail',
                title: 'Enviar Capacitación'
            },
            data: {
                action: '/industrialSecurity/roadsafety/training/sendNotification/',
                id: 'id',
                messageConfirmation: 'Esta seguro de enviar la capacitación'
            },
            permission: 'roadsafety_trainings_u'
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/training/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-roadsafety-trainings-employees',
    fields: [
        { name: 'sau_rs_training_employee_attempts.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
        { name: 'sau_employees.name', data: 'employee', title: 'Empleado', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_training_employee_attempts.attempt', data: 'attempt', title: 'Número de intento', sortable: true, searchable: true, detail: false, key: false },
        { name: 'sau_rs_training_employee_attempts.state', data: 'state_attempts', title: 'Estado', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'industrialsecure-roadsafety-trainings-employee-view' },
              id: 'id',
          },
          permission: 'roadsafety_trainings_r'
          }]
      },
      {
          type: 'base',
          buttons: [],
      }],
    configuration: {
        urlData: '/industrialSecurity/roadsafety/training/dataEmployee',
        filterColumns: true,
        //configNameFilter: 'legalaspects-evaluations-contracts'
    }
},
];
