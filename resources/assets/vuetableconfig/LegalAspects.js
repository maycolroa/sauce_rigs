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
            { name: 'sau_ct_information_contract_lessee.nit', data: 'nit', title: 'Contratista', sortable: true, searchable: true, detail: false, key: false },
            { name: 'date', data: 'date', title: 'Fecha creación', sortable: true, searchable: false, detail: false, key: false },
            { name: 'creator', data: 'creator', title: 'Creador', sortable: true, searchable: false, detail: false, key: false },
            { name: 'evaluation_date', data: 'evaluation_date', title: 'Fecha evaluación', sortable: true, searchable: true, detail: false, key: false },
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
                    routePush: { name: 'legalaspects-evaluations-edit' },
                    id: 'id',
                },
            }, {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
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
                },
                data: {
                    routePush: { name: 'legalaspects-evaluations-evaluate' },
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
        }
    }
]