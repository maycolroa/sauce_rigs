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
        },
        data: {
            routePush: { name: 'industrialsecure-activities-edit' },
            id: 'id',
        }
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
        },
        data: {
            routePush: { name: 'industrialsecure-activities-view' },
            id: 'id',
        }
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
        },
        data: {
            routePush: { name: 'industrialsecure-dangers-edit' },
            id: 'id',
        }
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
        },
        data: {
            routePush: { name: 'industrialsecure-dangers-view' },
            id: 'id',
        }
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
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/danger/data',
        filterColumns: true,
    }
},
{
    name: 'industrialsecure-dangermatrix',
    fields: [],
    'controlls': [{
        type: 'push',
        buttons: [{
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-create',
        },
        data: {
            routePush: { name: 'industrialsecure-dangermatrix-edit' },
            id: 'id',
        }
        }, {
        config: {
            color: 'outline-info',
            borderless: true,
            icon: 'ion ion-md-eye',
        },
        data: {
            routePush: { name: 'industrialsecure-dangermatrix-view' },
            id: 'id',
        }
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
        }],
    }],
    configuration: {
        urlData: '/industrialSecurity/dangersMatrix/data',
        filterColumns: true,
    }
}
];
