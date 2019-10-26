export default [
    {
    name: 'biologicalmonitoring-audiometry',
    fields : [
        {name : 'sau_bm_audiometries.id', data:'id', title : 'ID',sortable: false, searchable : false, detail : false, key : true},
        {name : 'sau_bm_audiometries.date', data:'date', title : 'Fecha',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_bm_audiometries.severity_grade_air_right_pta', data:'severity_grade_air_right_pta', title : 'Grado de severidad aereo derecho PTA',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_bm_audiometries.severity_grade_air_left_pta', data:'severity_grade_air_left_pta', title : 'Grado de severidad aereo izquierdo PTA',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_employees.identification', data:'identification', title : 'Identificación empleado',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_employees.name', data:'name', title : 'Nombre empleado',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_employees_regionals.name', data:'regional', title: 'Regional', sortable: true, searchable: true, detail: false, key: false},
        {name : 'sau_employees.deal', data:'deal', title: 'Negocio', sortable: true, searchable: true, detail: false, key: false},
        {name : 'base_si_no', data:'base_si_no', title : 'Base', sortable: false, searchable : false, detail : false, key : false},
        {name : 'base_state', data:'base_state', title : 'Tipo Base', sortable: true, searchable : true, detail : false, key : false},
        {name : '', data:'controlls', title : 'Controles',sortable: false, searchable : false, detail : false, key : false},
    ],
    'controlls' : [{
      type: 'push',
      buttons:[{
        config: {
          color:'outline-success',
          borderless: true,
          icon:'ion ion-md-create',
          title: 'Editar'
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-edit'},
          id: 'id',
        },
        permission: 'biologicalMonitoring_audiometry_u'
      },{
        config: {
          color:'outline-info',
          borderless: true,
          icon:'ion ion-md-eye',
          title: 'Ver'
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-view'},
          id: 'id',
        },
        permission: 'biologicalMonitoring_audiometry_r'
      },{
        config: {
          color:'outline-success',
          borderless: true,
          icon:'fas fa-chart-line',
          title: 'Ver Reporte'
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-report'},
          id: 'id',
        },
        permission: 'biologicalMonitoring_audiometry_r'
      },{
        config: {
          color:'outline-info',
          borderless: true,
          icon:'ion ion-md-clipboard',
          title: 'Ver Informe Individual'
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-informs-individual'},
          id: 'employee_id',
        },
        permission: 'biologicalMonitoring_audiometry_inform_individual_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name:'delete',
        data: {
          action:'/biologicalmonitoring/audiometry/',
          id: 'id',
          messageConfirmation : 'Esta seguro de borrar la audiometria del __date__ para el empleado __employee_identification__ - __employee_name__'
        },
        permission: 'biologicalMonitoring_audiometry_d'
      }],
    }],
    configuration: {
        urlData: '/biologicalmonitoring/audiometry/data',
        filterColumns: true,
        detailComponent: '/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/DetailVuetableAudiometryComponent.vue',
        configNameFilter: 'biologicalmonitoring-audiometry'
    }
},
{
    name: 'biologicalmonitoring-audiometry-employee',
    fields : [
        {name : 'sau_bm_audiometries.id', data:'id', title : 'ID',sortable: false, searchable : false, detail : false, key : true},
        {name : 'sau_bm_audiometries.date', data:'date', title : 'Fecha',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_bm_audiometries.severity_grade_air_right_pta', data:'severity_grade_air_right_pta', title : 'Grado de severidad aereo derecho PTA',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_bm_audiometries.severity_grade_air_left_pta', data:'severity_grade_air_left_pta', title : 'Grado de severidad aereo izquierdo PTA',sortable: true, searchable : true, detail : false, key : false},
        {name : 'base_si_no', data:'base_si_no', title : 'Base', sortable: false, searchable : false, detail : false, key : false},
        {name : 'base_state', data:'base_state', title : 'Tipo Base', sortable: true, searchable : true, detail : false, key : false},
        {name : '', data:'controlls', title : 'Controles',sortable: false, searchable : false, detail : false, key : false},
    ],
    'controlls' : [{
      type: 'push',
      buttons:[{
        config: {
          color:'outline-success',
          borderless: true,
          icon:'ion ion-md-create',
          title: 'Editar'
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-edit'},
          id: 'id',
        },
        permission: 'biologicalMonitoring_audiometry_u'
      },{
        config: {
          color:'outline-info',
          borderless: true,
          icon:'ion ion-md-eye',
          title: 'Ver'
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-view'},
          id: 'id',
        },
        permission: 'biologicalMonitoring_audiometry_r'
      },{
        config: {
          color:'outline-success',
          borderless: true,
          icon:'fas fa-chart-line',
          title: 'Ver Reporte'
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-report'},
          id: 'id',
        },
        permission: 'biologicalMonitoring_audiometry_r'
      }]
    },
    {
      type: 'base',
      buttons: [{
        name:'delete',
        data: {
          action:'/biologicalmonitoring/audiometry/',
          id: 'id',
          messageConfirmation : 'Esta seguro de borrar la audiometria del __date__ para el empleado __employee_identification__ - __employee_name__'
        },
        permission: 'biologicalMonitoring_audiometry_d'
      }],
    }],
    configuration: {
        urlData: '/biologicalmonitoring/audiometry/data',
        filterColumns: true,
        detailComponent: '/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/DetailVuetableAudiometryComponent.vue'
    }
},
{
  name: 'reinstatements-restrictions',
  fields: [
      { name: 'sau_reinc_restrictions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_reinc_restrictions.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'reinstatements-restrictions-edit' },
              id: 'id',
          },
          permission: 'reinc_restrictions_u'
      }, {
          config: {
              color: 'outline-info',
              borderless: true,
              icon: 'ion ion-md-eye',
              title: 'Ver'
          },
          data: {
              routePush: { name: 'reinstatements-restrictions-view' },
              id: 'id',
          },
          permission: 'reinc_restrictions_r'
      }]
  },
  {
      type: 'base',
      buttons: [{
      name: 'delete',
      data: {
          action: '/biologicalmonitoring/reinstatements/restriction/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar la Restricción __name__'
      },
      permission: 'reinc_restrictions_d'
      }],
  }],
  configuration: {
      urlData: '/biologicalmonitoring/reinstatements/restriction/data',
      filterColumns: true,
  }
},
{
  name: 'reinstatements-checks',
  fields: [
      { name: 'sau_reinc_checks.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
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
              routePush: { name: 'reinstatements-checks-edit' },
              id: 'id',
          },
          permission: 'reinc_checks_u'
      }, {
          config: {
              color: 'outline-info',
              borderless: true,
              icon: 'ion ion-md-eye',
              title: 'Ver'
          },
          data: {
              routePush: { name: 'reinstatements-checks-view' },
              id: 'id',
          },
          permission: 'reinc_checks_r'
      }, {
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-clipboard',
            title: 'Generar Carta'
        },
        data: {
            routePush: { name: 'reinstatements-checks-letter' },
            id: 'id',
        }
    }]
  },
  {
    type: 'form',
    buttons: [
      {
        name: 'switchStatus',
        config: {
            color: 'outline-danger',
            borderless: true,
            icon: 'fas fa-sync',
            title: 'Cambiar estado'
        },
        data: {
            action: '/biologicalmonitoring/reinstatements/check/switchStatus',
            id: 'id'
        },
        permission: 'reinc_checks_u'
      }
    ],
  },
  {
      type: 'base',
      buttons: [      
        {
        name: 'delete',
        data: {
            action: '/biologicalmonitoring/reinstatements/check/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el reporte __name__'
        },
        permission: 'reinc_checks_d'
      },
      
    ],
  }],
  configuration: {
      urlData: '/biologicalmonitoring/reinstatements/check/data',
      filterColumns: true
  }
},
{
  name: 'reinstatements-checks-form',
  fields: [
      { name: 'sau_reinc_checks.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
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
              routePush: { name: 'reinstatements-checks-edit' },
              id: 'id',
          },
          permission: 'reinc_checks_u'
      }, {
          config: {
              color: 'outline-info',
              borderless: true,
              icon: 'ion ion-md-eye',
              title: 'Ver'
          },
          data: {
              routePush: { name: 'reinstatements-checks-view' },
              id: 'id',
          },
          permission: 'reinc_checks_r'
      },{
        config: {
            color: 'outline-success',
            borderless: true,
            icon: 'ion ion-md-clipboard',
            title: 'Generar Carta'
        },
        data: {
            routePush: { name: 'reinstatements-checks-letter' },
            id: 'id',
        }
    }]
  },
  {
      type: 'base',
      buttons: [      
    ],
  }],
  configuration: {
      urlData: '/biologicalmonitoring/reinstatements/check/data',
      filterColumns: true
  }
},
{
  name: 'biologicalmonitoring-musculoskeletalAnalysis',
  fields: [
      { name: 'sau_bm_musculoskeletal_analysis.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_bm_musculoskeletal_analysis.patient_identification', data: 'patient_identification', title: 'Identificación Paciente', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.company', data: 'company', title: 'Empresa', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.branch_office', data: 'branch_office', title: 'Regional o Planta', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.consolidated_personal_risk_criterion', data: 'consolidated_personal_risk_criterion', title: 'Consolidado Riesgo Personal (Criterio)', sortable: true, searchable: true, detail: false, key: false }
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
      urlData: '/biologicalmonitoring/musculoskeletalAnalysis/data',
      filterColumns: true,
      configNameFilter: 'biologicalmonitoring-musculoskeletalAnalysis'
  }
},
{
  name: 'absenteeism-reports',
  fields: [
      { name: 'sau_absen_reports.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_absen_reports.name_show', data: 'name_show', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'absenteeism-reports-edit' },
                id: 'id',
            },
            permission: 'absen_reports_u'
        }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'absenteeism-reports-view' },
                id: 'id',
            },
            permission: 'absen_reports_r'
        },{
          config: {
              color: 'outline-info',
              borderless: true,
              icon: 'ion ion-md-person-add',
              title: 'Agregar usuarios al reporte'
          },
          data: {
              routePush: { name: 'absenteeism-reports-user-add' },
              id: 'id',
          },
          permission: 'absen_reports_admin_user'
      }]
  },
  {
      type: 'base',
      buttons: [{
      name: 'delete',
      data: {
          action: '/biologicalmonitoring/absenteeism/report/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el reporte __name__'
      },
      permission: 'absen_reports_d'
      }],
  }],
  configuration: {
      urlData: '/biologicalmonitoring/absenteeism/report/data',
      filterColumns: true,
  }
},
{
  name: 'absenteeism-fileUpload',
  fields: [
      { name: 'sau_absen_file_upload.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_absen_file_upload.name', data: 'name', title: 'Nombre del archivo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_users.name', data: 'user_name', title: 'Usuario', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_absen_file_upload.created_at', data: 'created_at', title: 'Fecha de subida', sortable: true, searchable: true, detail: false, key: false },
      { name: '', data: 'controlls', title: 'Controles', sortable: false, searchable: false, detail: false, key: false },
  ],
  'controlls': [
    {
      type: 'simpleDownload',

      buttons: [{

          name: 'downloadFile',
          
          config: {
              color: 'outline-success',
              borderless: true,
              icon: 'ion ion-md-cloud-download',
              title: 'Descargar Archivo'
          },
          data: {
              action: '/biologicalmonitoring/absenteeism/fileUpload/download/',
              id: 'id'
          },
          permission: 'absen_uploadFiles_r'
      }],
  },{
        type: 'push',
        buttons: []
  },
  {
      type: 'base',
      buttons: []
  }],
  configuration: {
      urlData: '/biologicalmonitoring/absenteeism/fileUpload/data',
      filterColumns: true,
  }
},
{
  name: 'absenteeism-talends',
  fields: [
      { name: 'sau_absen_talends.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_absen_talends.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_absen_talends.state', data: 'state', title: '¿Activo?', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_absen_talends.created_at', data: 'created_at', title: 'Fecha Creación', sortable: true, searchable: true, detail: false, key: false },
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
              routePush: { name: 'absenteeism-talends-edit' },
              id: 'id',
          },
          permission: 'absen_uploadTalend_u'
      }, {
          config: {
              color: 'outline-info',
              borderless: true,
              icon: 'ion ion-md-eye',
              title: 'Ver'
          },
          data: {
              routePush: { name: 'absenteeism-talends-view' },
              id: 'id',
          },
          permission: 'absen_uploadTalend_r'
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
            title: 'Cambiar estado'
        },
        data: {
            action: '/biologicalmonitoring/absenteeism/talendUpload/switchStatus/',
            id: 'id',
            messageConfirmation: 'Esta seguro de querer cambiar el estado del talend __display_name__'
        },
        permission: 'absen_uploadTalend_d'
      }
    ],
  }],
  configuration: {
      urlData: '/biologicalmonitoring/absenteeism/talendUpload/data',
      filterColumns: true
  }
}
];
