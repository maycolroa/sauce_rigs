export default [
  {
    name: 'administrative-users',
    fields: [
      { name: 'sau_users.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_users.name', data: 'name', title: 'Nombre', sortable: true, searchable: false, detail: false, key: false },
      { name: 'sau_users.email', data: 'email', title: 'Email', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.document', data: 'document', title: 'Documento', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.document_type', data: 'document_type', title: 'Tipo de Documento', sortable: true, searchable: true, detail: false, key: false },
      { name: 'role', data: 'role', title: 'Rol', sortable: false, searchable: false, detail: false, key: false },
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
          routePush: { name: 'administrative-users-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-users-view' },
          id: 'id',
        }
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
      }],
    }],
    configuration: {
      urlData: '/administration/users/data',
      filterColumns: true,
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
        },
        data: {
          routePush: { name: 'administrative-roles-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-roles-view' },
          id: 'id',
        }
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
        },
        data: {
          routePush: { name: 'administrative-positions-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-positions-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/position/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el cargo __name__'
        },
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
        },
        data: {
          routePush: { name: 'administrative-regionals-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-regionals-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/regional/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar la regional __name__'
        },
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
        },
        data: {
          routePush: { name: 'administrative-businesses-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-businesses-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/business/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el centro de costo __name__'
        },
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
      { name: 'sau_employees_headquarters.name', data: 'name', title: 'Nombre', sortable: true, searchable: false, detail: false, key: false },
      { name: 'regional', data: 'regional', title: 'Regional', sortable: true, searchable: false, detail: false, key: false },
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
          routePush: { name: 'administrative-headquarters-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-headquarters-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/headquarter/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar la sede __name__'
        },
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
      { name: 'sau_employees_areas.name', data: 'name', title: 'Nombre', sortable: true, searchable: false, detail: false, key: false },
      { name: 'sede', data: 'sede', title: 'Sede', sortable: true, searchable: false, detail: false, key: false },
      { name: 'regional', data: 'regional', title: 'Regional', sortable: true, searchable: false, detail: false, key: false },
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
          routePush: { name: 'administrative-areas-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-areas-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/area/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el área __name__'
        },
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
      { name: 'sau_employees_processes.name', data: 'name', title: 'Nombre', sortable: true, searchable: false, detail: false, key: false },
      { name: 'area', data: 'area', title: 'Área', sortable: true, searchable: false, detail: false, key: false },
      { name: 'sede', data: 'sede', title: 'Sede', sortable: true, searchable: false, detail: false, key: false },
      { name: 'regional', data: 'regional', title: 'Regional', sortable: true, searchable: false, detail: false, key: false },
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
          routePush: { name: 'administrative-processes-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-processes-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/process/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el proceso __name__'
        },
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
      { name: 'sau_employees_employees.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_employees_employees.identification', data: 'identification', title: 'Identificación', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_employees.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sex_detail', data: 'sex_detail', title: 'Sexo', sortable: false, searchable: false, detail: false, key: false },
      { name: 'sau_employees_employees.email', data: 'email', title: 'Email', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_employees_employees.income_date', data: 'income_date', title: 'Fecha de Ingreso', sortable: true, searchable: true, detail: false, key: false },
      /*{ name: 'cargo', data: 'cargo', title: 'Cargo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'regional', data: 'regional', title: 'Regional', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sede', data: 'sede', title: 'Sede', sortable: true, searchable: true, detail: false, key: false },
      { name: 'area', data: 'area', title: 'Área', sortable: true, searchable: true, detail: false, key: false },*/
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
          routePush: { name: 'administrative-employees-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'administrative-employees-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/administration/employee/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el empleado __name__'
        },
      }],
    }],
    configuration: {
      urlData: '/administration/employee/data',
      filterColumns: true,
    }
  },
  {
    name: 'configurations-locationlevelform',
    fields : [
        {name: 'sau_conf_location_level_forms.id', data: 'id', title:'ID', sortable: false, searchable: false, detail: false, key: true},
        {name: 'module', data:'module', title: 'Módulo', sortable: false, searchable: false, detail : false, key : false},
        {name: 'sau_conf_location_level_forms.regional', data:'regional', title: 'Regional',sortable: true, searchable : true, detail : false, key : false},
        {name: 'sau_conf_location_level_forms.headquarter', data:'headquarter', title : 'Sede',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_conf_location_level_forms.area', data:'area', title : 'Área',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_conf_location_level_forms.process', data:'process', title : 'Proceso ',sortable: true, searchable : true, detail : false, key : false},
        {name : '', data:'controlls', title : 'Controles',sortable: false, searchable : false, detail : false, key : false},
    ],
    'controlls' : [{
      type: 'push',
      buttons:[{
        config: {
          color:'outline-success',
          borderless: true,
          icon:'ion ion-md-create',
        },
        data:{
          routePush: {name: 'configurations-locationlevelform-edit'},
          id: 'id',
        }
      },{
        config: {
          color:'outline-info',
          borderless: true,
          icon:'ion ion-md-eye',
        },
        data:{
          routePush: {name: 'configurations-locationlevelform-view'},
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name:'delete',
        data: {
          action:'/administration/configurations/locationLevelForms/',
          id: 'id',
          messageConfirmation : 'Esta seguro de borrar la configuración'
        },
      }],
    }],
    configuration: {
        urlData: '/administration/configurations/locationLevelForms/data',
        filterColumns: true
    }
  }
];
