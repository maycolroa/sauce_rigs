export default [
  {
    name: 'legalAspects-fileUpload',
    fields: [
      { name: 'sau_ct_file_upload_contracts_leesse.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_ct_file_upload_contracts_leesse.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_ct_file_upload_contracts_leesse.expirationDate', data: 'expirationDate', title: 'Fecha Vencimiento', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.name', data: 'Usuario', title: 'Nombre Usuario', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_ct_file_upload_contracts_leesse.created_at', data: 'created_at', title: 'Fecha Creación', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_ct_file_upload_contracts_leesse.updated_at', data: 'updated_at', title: 'Fecha Actualización', sortable: true, searchable: true, detail: false, key: false },
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
          routePush: { name: 'legalaspects-upload-files-edit' },
          id: 'id',
        }
      }, {
        config: {
          color: 'outline-info',
          borderless: true,
          icon: 'ion ion-md-eye',
        },
        data: {
          routePush: { name: 'legalaspects-upload-files-view' },
          id: 'id',
        }
      }]
    },
    {
      type: 'base',
      buttons: [{
        name: 'delete',
        data: {
          action: '/legalAspects/fileUpload/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el archivo del __name__'
        }
      }],
    }],
    configuration: {
      urlData: '/legalAspects/fileUpload/data',
      filterColumns: true,
    }
  }]