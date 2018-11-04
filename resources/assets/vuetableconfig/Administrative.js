export default [
  {
    name: 'administrative-users',
    fields: [
      { name: 'sau_users.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_users.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.email', data: 'email', title: 'Email', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.document', data: 'document', title: 'Documento', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.document_type', data: 'document_type', title: 'Tipo de Documento', sortable: true, searchable: true, detail: false, key: false },
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
      { name: 'roles.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'roles.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'roles.description', data: 'description', title: 'Descripción', sortable: true, searchable: true, detail: false, key: false },
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
  }
];
