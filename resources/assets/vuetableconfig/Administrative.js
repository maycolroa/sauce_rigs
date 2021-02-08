export default [
  {
    name: 'administrative-users',
    fields: [
      { name: 'sau_users.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
          routePush: { name: 'administrative-users-edit' },
          id: 'id',
        },
        permission: 'users_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-users-view' },
          id: 'id',
        },
        permission: 'users_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/users/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el usuario del __name__'
        },
        permission: 'users_d'
      }],
    }],
    configuration: {
      urlData: '/administration/users/data',
      filterColumns: true,
      configNameFilter: 'administrative-users'
    }
  },
  {
    name: 'administrative-roles',
    fields: [
      { name: 'sau_roles.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
          routePush: { name: 'administrative-roles-edit' },
          id: 'id',
        },
        permission: 'roles_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-roles-view' },
          id: 'id',
        },
        permission: 'roles_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/role/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el rol del __name__'
        },
        permission: 'roles_d'
      }],
    }],
    configuration: {
      urlData: '/administration/role/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-positions',
    fields: [
      { name: 'sau_employees_positions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_employees_positions.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'administrative-positions-edit' },
          id: 'id',
        },
        permission: 'positions_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-positions-view' },
          id: 'id',
        },
        permission: 'positions_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/position/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el registro __name__'
        },
        permission: 'positions_d'
      }],
    }],
    configuration: {
      urlData: '/administration/position/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-regionals',
    fields: [
      { name: 'sau_employees_regionals.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_employees_regionals.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'administrative-regionals-edit' },
          id: 'id',
        },
        permission: 'regionals_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-regionals-view' },
          id: 'id',
        },
        permission: 'regionals_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/regional/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el registro __name__'
        },
        permission: 'regionals_d'
      }],
    }],
    configuration: {
      urlData: '/administration/regional/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-businesses',
    fields: [
      { name: 'sau_employees_businesses.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_employees_businesses.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'administrative-businesses-edit' },
          id: 'id',
        },
        permission: 'businesses_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-businesses-view' },
          id: 'id',
        },
        permission: 'businesses_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/business/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el registro __name__'
        },
        permission: 'businesses_d'
      }],
    }],
    configuration: {
      urlData: '/administration/business/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-headquarters',
    fields: [
      { name: 'sau_employees_headquarters.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_employees_headquarters.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_regionals.name', data: 'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'administrative-headquarters-edit' },
          id: 'id',
        },
        permission: 'headquarters_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-headquarters-view' },
          id: 'id',
        },
        permission: 'headquarters_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/headquarter/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el registro __name__'
        },
        permission: 'headquarters_d'
      }],
    }],
    configuration: {
      urlData: '/administration/headquarter/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-areas',
    fields: [
      { name: 'sau_employees_areas.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_employees_areas.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_processes.name', data: 'proceso', title: 'process', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_headquarters.name', data: 'sede', title: 'headquarter', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_regionals.name', data: 'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'administrative-areas-edit' },
          id: 'id',
        },
        permission: 'areas_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-areas-view' },
          id: 'id',
        },
        permission: 'areas_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/area/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el registro __name__'
        },
        permission: 'areas_d'
      }],
    }],
    configuration: {
      urlData: '/administration/area/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-processes',
    fields: [
      { name: 'sau_employees_processes.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_employees_processes.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_headquarters.name', data: 'sede', title: 'headquarter', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_regionals.name', data: 'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'administrative-processes-edit' },
          id: 'id',
        },
        permission: 'processes_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-processes-view' },
          id: 'id',
        },
        permission: 'processes_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/process/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el registro __name__'
        },
        permission: 'processes_d'
      }],
    }],
    configuration: {
      urlData: '/administration/process/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-employees',
    fields: [
      { name: 'sau_employees.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true }
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
          routePush: { name: 'administrative-employees-edit' },
          id: 'id',
        },
        permission: 'employees_u'
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
          title: 'Ver'
        },
        data: {
          routePush: { name: 'administrative-employees-view' },
          id: 'id',
        },
        permission: 'employees_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/employee/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el registro __name__'
        },
        permission: 'employees_d'
      }],
    }],
    configuration: {
      urlData: '/administration/employee/data',
      filterColumns: true,
    }
  },
  {
    name: 'administrative-actionplans',
    fields: [
      { name: 'sau_action_plans_activities.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_action_plans_activities.description', data: 'description', title: 'Descripcion', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.name', data: 'responsible', title: 'Responsable', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_action_plans_activities.expiration_date', data: 'expiration_date', title: 'Fecha Vencimiento', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_action_plans_activities.execution_date', data: 'execution_date', title: 'Fecha Ejecución', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_action_plans_activities.state', data: 'state_activity', title: 'Estado', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_modules.display_name', data: 'display_name', title: 'Módulo', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'administrative-actionplans-edit' },
          id: 'id',
        },
        permission: 'actionPlans_u'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
            action: '/administration/actionplan/deleteActivity/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la actividad'
        },
        permission: 'action_plan_activities_d'
        }],
    }],
    configuration: {
      urlData: '/administration/actionplan/data',
      filterColumns: true,
      configNameFilter: 'administrative-actionplans'
    }
  },
  {
      name: 'administrative-labels',
      fields: [
          { name: 'sau_keywords.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
          { name: 'sau_keywords.display_name', data: 'name', title: 'Etiqueta', sortable: true, searchable: true, detail: false, key: false },
          { name: 'sau_keyword_company.display_name', data: 'display_name', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'administrative-customlabels-edit' },
              id: 'id',
          },
          permission: 'customLabels_u'
          }, {
          config: {
              color: 'outline-info',
              borderless: true,
              icon: 'ion ion-md-eye',
              title: 'Ver'
          },
          data: {
              routePush: { name: 'administrative-customlabels-view' },
              id: 'id',
          },
          permission: 'customLabels_r'
          }]
      },
      {
          type: 'base',
          buttons: [{
            name: 'delete',
            data: {
              action: '/administration/label/',
              id: 'id',
              messageConfirmation: 'Esta seguro de borrar la etiqueta __name__'
            },
            permission: 'customLabels_d'
          }],
      }],
      configuration: {
          urlData: '/administration/label/data',
          filterColumns: true,
      }
  }
];
