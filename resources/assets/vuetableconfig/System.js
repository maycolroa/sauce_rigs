export default [
    {
        name: 'system-licenses',
        fields: [
            { name: 'sau_licenses.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_companies.name', data: 'company', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.started_at', data: 'started_at', title: 'Fecha Inicio', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.ended_at', data: 'ended_at', title: 'Fecha Fin', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.created_at', data: 'created_at', title: 'Fecha Creación', sortable: true, searchable: true, detail: false, key: false },
            { name: 'modules', data: 'modules', title: '#Módulos', sortable: true, searchable: false, detail: false, key: false },
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
                routePush: { name: 'system-licenses-edit' },
                id: 'id',
            },
            permission: 'licenses_u'
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'system-licenses-view' },
                id: 'id',
            },
            permission: 'licenses_r'
            }]
        },
        {
            type: 'base',
            buttons: [{
            name: 'delete',
            data: {
                action: '/system/license/',
                id: 'id',
                messageConfirmation: 'Esta seguro de borrar la licencia __name__'
            },
            permission: 'licenses_d'
            }],
        }],
        configuration: {
            urlData: '/system/license/data',
            filterColumns: true,
        }
    },
    {
        name: 'system-license-histories',
        fields: [
            { name: 'sau_license_histories.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_users.name', data: 'name', title: 'Responsable', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_license_histories.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: false, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/license/history/data',
            filterColumns: true,
        }
    },
    {
        name: 'system-logmails',
        fields: [
            { name: 'sau_log_mails.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_modules.name', data: 'module', title: 'Módulo', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_log_mails.event', data: 'event', title: 'Evento', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_log_mails.subject', data: 'subject', title: 'Asunto', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_log_mails.created_at', data: 'created_at', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
            { event: 'sau_log_mails.recipients', data: 'recipients', title: 'Destinatarios', sortable: true, searchable: true, detail: false, key: false },
            { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
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
                        routePush: { name: 'system-logmails-view' },
                        id: 'id',
                    },
                    permission: 'logMails_r'
                }]
            },
            {
                type: 'base',
                buttons: [],
            }],
        configuration: {
            urlData: '/system/logMail/data',
            filterColumns: true,
        }
    }
]