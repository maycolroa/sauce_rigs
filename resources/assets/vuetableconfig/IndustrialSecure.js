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
}
];
