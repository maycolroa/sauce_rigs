export default [
    {
        name: 'system-licenses',
        fields: [
            { name: 'sau_licenses.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_companies.name', data: 'company', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_company_groups.name', data: 'group_company', title: 'Grupo de Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.started_at', data: 'started_at', title: 'Fecha Inicio', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.ended_at', data: 'ended_at', title: 'Fecha Fin', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.created_at', data: 'created_at', title: 'Fecha Creación', sortable: true, searchable: true, detail: false, key: false },
            { name: 'modules', data: 'modules', title: 'Módulos', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_licenses.freeze', data: 'freeze', title: '¿Congelada?', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.date_freeze', data: 'date_freeze', title: 'Fecha de congelamiento', sortable: true, searchable: true, detail: false, key: false },
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
            configNameFilter: 'system-licenses'
        }
    },
    {
        name: 'system-licenses-reasignacion',
        fields: [
            { name: 'sau_licenses.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_companies.name', data: 'company', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_company_groups.name', data: 'group_company', title: 'Grupo de Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.started_at', data: 'started_at', title: 'Fecha Inicio', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.ended_at', data: 'ended_at', title: 'Fecha Fin', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_licenses.created_at', data: 'created_at', title: 'Fecha Creación', sortable: true, searchable: true, detail: false, key: false },
            { name: 'modules', data: 'modules', title: 'Módulos', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_licenses.freeze', data: 'freeze', title: '¿Congelada?', sortable: true, searchable: true, detail: false, key: false },
            { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
        ],
        'controlls': [{
            type: 'push',
            buttons: [{
            config: {
                color: 'outline-success',
                borderless: true,
                icon: 'ion ion-md-create',
                title: 'Reasignar'
            },
            data: {
                routePush: { name: 'system-licenses-reasignar' },
                id: 'id',
            },
            permission: 'licenses_u'
            }]
        },
        {
            type: 'base',
            buttons: [],
        }],
        configuration: {
            urlData: '/system/license/dataReasignation',
            filterColumns: true,
            configNameFilter: 'system-licenses-reasignacion'
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
            { name: 'sau_companies.name', data: 'company', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
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
            configNameFilter: 'system-logmails'
        }
    },
    {
        name: 'system-labels',
        fields: [
            { name: 'sau_keywords.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_keywords.name', data: 'name', title: 'Key', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_keywords.display_name', data: 'display_name', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'system-labels-edit' },
                id: 'id',
            },
            permission: 'labels_u'
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'system-labels-view' },
                id: 'id',
            },
            permission: 'labels_r'
            }]
        },
        {
            type: 'base',
            buttons: [],
        }],
        configuration: {
            urlData: '/system/label/data',
            filterColumns: true,
        }
    },
    {
        name: 'system-companies',
        fields: [
            { name: 'sau_companies.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_companies.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_company_groups.name', data: 'group', title: 'Grupo de compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_companies.active', data: 'active', title: '¿Activo?', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_companies.test', data: 'test', title: '¿Prueba?', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'system-companies-edit' },
                id: 'id',
            },
            permission: 'companies_u'
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'system-companies-view' },
                id: 'id',
            },
            permission: 'companies_r'
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
                        action: '/system/company/switchStatus/',
                        id: 'id',
                        messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                    },
                    permission: 'companies_u'
                }
            ],
        }],
        configuration: {
            urlData: '/system/company/data',
            filterColumns: true,
            configNameFilter: 'system-companies'
        }
    },
    {
        name: 'system-users-companies',
        fields: [
            { name: 'sau_users.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_users.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_users.email', data: 'email', title: 'Correo', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_companies.name', data: 'company', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/usersCompanies/data',
            filterColumns: true,
            configNameFilter: 'system-userscompany'
        }
    },
    {
        name: 'system-customermonitoring-reinstatements',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'total_mes', data: 'total_mes', title: 'Reṕortes creados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'total_anio', data: 'total_anio', title: 'Reṕortes creados este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataReinstatements',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-dangerousConditions',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'report_mes', data: 'report_mes', title: 'Reṕortes creados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'report_anio', data: 'report_anio', title: 'Reṕortes creados este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'insp_mes', data: 'insp_mes', title: 'Inspeciones realizadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'insp_anio', data: 'insp_anio', title: 'Inspeciones realizadas este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataDangerousConditions',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-absenteeism',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'report_mes', data: 'report_mes', title: 'Reṕortes vistos este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'report_anio', data: 'report_anio', title: 'Reṕortes vistos este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'file_mes', data: 'file_mes', title: 'Archivos cargados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'file_anio', data: 'file_anio', title: 'Archivos cargados este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataAbsenteeism',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-dangerMatrix',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'total_mes', data: 'total_mes', title: 'Matríces creadas o modificadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'total_anio', data: 'total_anio', title: 'Matríces creadas o modificadas este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataDangerMatrix',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-riskMatrix',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'total_mes', data: 'total_mes', title: 'Matríces creadas o modificadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'total_anio', data: 'total_anio', title: 'Matríces creadas o modificadas este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataRiskMatrix',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-contract',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'contract_mes', data: 'contract_mes', title: 'Contratistas creados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'contract_anio', data: 'contract_anio', title: 'Contratistas creados este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'eva_mes', data: 'eva_mes', title: 'Evaluaciones creadas o modificadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'eva_anio', data: 'eva_anio', title: 'Evaluaciones creadas o modificadas este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'cal_mes', data: 'cal_mes', title: 'Items de lista de chequeo calificados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'cal_anio', data: 'cal_anio', title: 'Items de lista de chequeo calificados este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'file_mes', data: 'file_mes', title: 'Archivos cargados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'file_anio', data: 'file_anio', title: 'Archivos cargados este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataContract',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-epp',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'element_mes', data: 'element_mes', title: 'Elementos creados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'element_anio', data: 'element_anio', title: 'Elementos creados este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'delivery_mes', data: 'delivery_mes', title: 'Entregas creadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'delivery_anio', data: 'delivery_anio', title: 'Entregas creadas este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'return_mes', data: 'return_mes', title: 'Devoluciones creadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'return_anio', data: 'return_anio', title: 'Devoluciones creadas este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'incomen_mes', data: 'incomen_mes', title: 'Ingresos creados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'incomen_anio', data: 'incomen_anio', title: 'Ingresos creados este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'exit_mes', data: 'exit_mes', title: 'Salidas creadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'exit_anio', data: 'exit_anio', title: 'Salidas creadas este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'transfer_mes', data: 'transfer_mes', title: 'Transferencias creadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'transfer_anio', data: 'transfer_anio', title: 'Transferencias creadas este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'reception_mes', data: 'reception_mes', title: 'Recepciones creadas este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'reception_anio', data: 'reception_anio', title: 'Recepciones creadas este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataEpp',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-legalMatrix',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'started_at', data: 'started_at', title: 'Fecha Inicio Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'ended_at', data: 'ended_at', title: 'Fecha Fin Licencia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'cal_mes', data: 'cal_mes', title: 'Artículos calificados este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'cal_anio', data: 'cal_anio', title: 'Artículos calificados este año', sortable: true, searchable: true, detail: false, key: false },
            { name: 'email_mes', data: 'email_mes', title: 'Emails vistos este mes', sortable: true, searchable: true, detail: false, key: false },
            { name: 'email_anio', data: 'email_anio', title: 'Emails vistos este año', sortable: true, searchable: true, detail: false, key: false }
        ],
        'controlls': [],
        configuration: {
            urlData: '/system/customermonitoring/dataLegalMatrix',
            filterColumns: true,
        }
    },
    {
        name: 'system-customermonitoring-automatics-send',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'name', data: 'name', title: 'Notificación', sortable: false, searchable: false, detail: false, key: false },
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
                routePush: { name: 'system-customermonitoring-automaticsSend-edit' },
                id: 'id',
            },
            permission: 'customerMonitoring_r'
            }]
        },
        {
            type: 'base',
            buttons: [],
        }],
        configuration: {
            urlData: '/system/customermonitoring/dataAutomaticsSend',
            filterColumns: true,
        }
    },
    {
        name: 'system-companygroup',
        fields: [
            { name: 'sau_company_groups.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_company_groups.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_company_groups.active', data: 'active', title: '¿Activo?', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'system-companygroup-edit' },
                id: 'id',
            },
            permission: 'companies_u'
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'system-companygroup-view' },
                id: 'id',
            },
            permission: 'companies_r'
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
                        action: '/system/companyGroup/switchStatus/',
                        id: 'id',
                        messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                    },
                    permission: 'companies_u'
                }
            ],
        }],
        configuration: {
            urlData: '/system/companyGroup/data',
            filterColumns: true,
        }
    },
    {
        name: 'system-newslettersend',
        fields: [
            { name: 'sau_newsletters_sends.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_newsletters_sends.subject', data: 'subject', title: 'Asunto', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_newsletters_sends.date_send', data: 'date_send', title: 'Fecha de envio', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_newsletters_sends.hour', data: 'hour', title: 'Hora de envio', sortable: true, searchable: true, detail: false, key: false },
            { name: 'active_send', data: 'active_send', title: '¿Activo?', sortable: true, searchable: true, detail: false, key: false },
            { name: 'send_2"', data: 'send_2', title: '¿Enviado?', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'system-newslettersend-edit' },
                id: 'id',
            },
            permission: 'newsletterSend_u'
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'system-newslettersend-view' },
                id: 'id',
            },
            permission: 'newsletterSend_r'
            }, {
                config: {
                    color: 'outline-success',
                    borderless: true,
                    icon: 'ion ion-md-clipboard',
                    title: 'Programar Envio'
                },
                data: {
                    routePush: { name: 'system-newslettersend-program' },
                    id: 'id'
                },
                permission: 'newsletterSend_r'
            },
            {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-list',
                    title: 'Ver Usuarios Receptores'
                },
                data: {
                    routePush: { name: 'system-newslettersend-opens' },
                    id: 'id',
                },
                permission: 'newsletterSend_r'
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
                        action: '/system/newsletterSend/switchStatus/',
                        id: 'id',
                        messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                    },
                    permission: 'newsletterSend_u'
                },
                {
                    name: 'retrySendMail',
                    config: {
                        color: 'outline-danger',
                        borderless: true,
                        icon: 'ion ion-ios-mail',
                        title: 'Enviarme boletin'
                    },
                    data: {
                        action: '/system/newsletterSend/sendMailManual/',
                        id: 'id',
                        messageConfirmation: 'Esta seguro de enviarse el boletin'
                    },
                    permission: 'newsletterSend_u'
                },
            ],
        }],
        configuration: {
            urlData: '/system/newsletterSend/data',
            filterColumns: true,
        }
    },
    {
        name: 'system-newslettersend-opens',
        fields: [
            { name: 'id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'user', data: 'user', title: 'Usuario', sortable: true, searchable: true, detail: false, key: false },
            { name: 'email', data: 'email', title: 'Email', sortable: true, searchable: true, detail: false, key: false },
            //{ name: 'company', data: 'company', title: 'Compañia', sortable: true, searchable: true, detail: false, key: false },
            { name: 'open', data: 'open', title: '¿Abierto?', sortable: true, searchable: true, detail: false, key: false },
            { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
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
            urlData: '/system/newsletterSend/data/opens',
            filterColumns: true,
        }
    },
    {
        name: 'system-helpers',
        fields: [
            { name: 'sau_helpers.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_helpers.title', data: 'title', title: 'Título', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_helpers.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_users.name', data: 'user', title: 'Usuario creador', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_modules.display_name', data: 'module', title: 'Modulo', sortable: true, searchable: true, detail: false, key: false },
            { name: 'sau_helpers.created_at', data: 'created_at', title: 'Fecha de creación', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'system-helpers-edit' },
                id: 'id',
            },
            permission: 'helpers_u'
            }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'system-helpers-view' },
                id: 'id',
            },
            permission: 'helpers_r'
            }]
        },
        {
            type: 'base',
            buttons: [{
                name: 'delete',
                data: {
                    action: '/system/helpers/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar la ayuda __title__'
                },
                permission: 'helpers_d'
                }],
        }],
        configuration: {
            urlData: '/system/helpers/data',
            filterColumns: true,
        }
    },
    {
        name: 'system-eps',
        fields: [
            { name: 'sau_employees_eps.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_employees_eps.code', data: 'code', title: 'Codigo', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_employees_eps.name', data: 'name', title: 'Nombre', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_employees_eps.state', data: 'state', title: '¿Activa?', sortable: true, searchable: false, detail: false, key: false },
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
                    routePush: { name: 'system-eps-edit' },
                    id: 'id',
                },
                permission: 'licenses_u'
            }, 
            {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'system-eps-view' },
                    id: 'id',
                },
                permission: 'licenses_r'
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
                        action: '/system/employeeEps/switchStatus/',
                        id: 'id',
                        messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                    },
                    permission: 'licenses_r'
                }
                /*{
                name: 'delete',
                data: {
                    action: '/system/employeeEps/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar la ayuda __title__'
                },
                permission: 'helpers_d'
                }*/],
        }],
        configuration: {
            urlData: '/system/employeeEps/data',
            filterColumns: true,
        }
    },
    {
        name: 'system-arl',
        fields: [
            { name: 'sau_employees_arl.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
            { name: 'sau_employees_arl.code', data: 'code', title: 'Codigo', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_employees_arl.name', data: 'name', title: 'Nombre', sortable: true, searchable: false, detail: false, key: false },
            { name: 'sau_employees_arl.state', data: 'state', title: '¿Activa?', sortable: true, searchable: false, detail: false, key: false },
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
                    routePush: { name: 'system-arl-edit' },
                    id: 'id',
                },
                permission: 'licenses_u'
            }, 
            {
                config: {
                    color: 'outline-info',
                    borderless: true,
                    icon: 'ion ion-md-eye',
                    title: 'Ver'
                },
                data: {
                    routePush: { name: 'system-arl-view' },
                    id: 'id',
                },
                permission: 'licenses_r'
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
                        action: '/system/employeeArl/switchStatus/',
                        id: 'id',
                        messageConfirmation: 'Esta seguro de querer cambiar el estado de __name__'
                    },
                    permission: 'licenses_r'
                } 
                /*{
                name: 'delete',
                data: {
                    action: '/system/employeeEps/',
                    id: 'id',
                    messageConfirmation: 'Esta seguro de borrar la ayuda __title__'
                },
                permission: 'helpers_d'
                }*/],
        }],
        configuration: {
            urlData: '/system/employeeArl/data',
            filterColumns: true,
        }
    },
]