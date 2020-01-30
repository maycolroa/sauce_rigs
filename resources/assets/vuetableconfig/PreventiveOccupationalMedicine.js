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
        {name : 'sau_employees_regionals.name', data:'regional', title: 'regional', sortable: true, searchable: true, detail: false, key: false},
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
  },
  {
      type: 'simpleDownload',
      buttons: [{
      name: 'downloadFile',
      config: {
        color: 'outline-danger',
        borderless: true,
        icon: 'fas fa-file-pdf',
        title: 'Descargar Reporte en PDF'
      },
      data: {
        action: '/biologicalmonitoring/reinstatements/check/downloadPdf/',
        id: 'id'
      },
      permission: 'reinc_checks_export'
      }],
  },],
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
  name: 'biologicalmonitoring-respiratoryAnalysis',
  fields: [
      { name: 'sau_bm_respiratory_analysis.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_bm_respiratory_analysis.patient_identification', data: 'patient_identification', title: 'Identificación Paciente', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.sex', data: 'sex', title: 'Sexo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.deal', data: 'deal', title: 'Negocio', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.regional', data: 'regional', title: 'Planta', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.date_of_birth', data: 'date_of_birth', title: 'Fecha de nacimiento', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.age', data: 'age', title: 'Edad', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.income_date', data: 'income_date', title: 'Fecha de ingreso a la empresa', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.antiquity', data: 'antiquity', title: 'Antiguedad', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.area', data: 'area', title: 'Área', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.position', data: 'position', title: 'Cargo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.habits', data: 'habits', title: 'Hábitos', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.history_of_respiratory_pathologies', data: 'history_of_respiratory_pathologies', title: 'Antecedentes de patologias respiratorias', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.measurement_date', data: 'measurement_date', title: 'Fecha de realización de mediciones', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.mg_m3_concentration', data: 'mg_m3_concentration', title: 'Concentración mg m3', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.ir', data: 'ir', title: 'IR', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.type_of_exam', data: 'type_of_exam', title: 'Tipo de examen', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.year_of_spirometry', data: 'year_of_spirometry', title: 'Año de espirometria', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.spirometry', data: 'spirometry', title: 'Espirometria', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.date_of_realization', data: 'date_of_realization', title: 'Fecha de realización', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.symptomatology', data: 'symptomatology', title: 'Sintomatologia', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.cvf_average_percentage', data: 'cvf_average_percentage', title: 'CVF % promedio', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.vef1_average_percentage', data: 'vef1_average_percentage', title: 'VEF 1% promedio', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.vef1_cvf_average', data: 'vef1_cvf_average', title: 'VEF1 / CVF % promedio', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.fef_25_75_porcentage', data: 'fef_25_75_porcentage', title: 'FEF 25-75%', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.interpretation', data: 'interpretation', title: 'Interpretación', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.type_of_exam_2', data: 'type_of_exam_2', title: 'Tipo de examen 2', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.date_of_realization_2', data: 'date_of_realization_2', title: 'Fecha de realizacion', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.rx_oit', data: 'rx_oit', title: 'RX OIT', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.quality', data: 'quality', title: 'Calidad', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.yes_1', data: 'yes_1', title: 'Sí 1', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.not_1', data: 'not_1', title: 'No 1', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.answer_yes_describe', data: 'answer_yes_describe', title: 'Respuesta sí, describir', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.yes_2', data: 'yes_2', title: 'Sí 2', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.not_2', data: 'not_2', title: 'No 2', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.answer_yes_describe_2', data: 'answer_yes_describe_2', title: 'Respuesta sí, describir', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.other_abnormalities', data: 'other_abnormalities', title: 'Otras Anormalidades', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.fully_negative', data: 'fully_negative', title: 'Totalmente negativa', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.observation', data: 'observation', title: 'Estado', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.breathing_problems', data: 'breathing_problems', title: 'Observación', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.classification_according_to_ats', data: 'classification_according_to_ats', title: 'Clasificación Según ATS', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.ats_obstruction_classification', data: 'ats_obstruction_classification', title: 'Clasificación obstrucción ATS', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.ats_restrictive_classification', data: 'ats_restrictive_classification', title: 'Clasificación Restrictivo ATS', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_respiratory_analysis.state', data: 'state', title: 'Estado', sortable: true, searchable: true, detail: false, key: false }
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
      urlData: '/biologicalmonitoring/respiratoryAnalysis/data',
      filterColumns: true,
      //configNameFilter: 'biologicalmonitoring-respiratoryAnalysis'
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
      { name: 'sau_absen_file_upload.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_absen_talends.name', data: 'talend_name', title: 'Tipo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_absen_file_upload.state', data: 'state', title: 'Estado', sortable: true, searchable: true, detail: false, key: false },
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
    },
    {
      type: 'push',
      buttons: []
    },
    {
      type: 'base',
      buttons: []
    }
  ],
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
},
{
  name: 'reinstatements-disease-origin',
  fields: [
      { name: 'sau_reinc_disease_origin.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_reinc_disease_origin.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'reinstatements-disease-origin-edit' },
                id: 'id',
            },
            permission: 'reinc_disease_origin_u'
        }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'reinstatements-disease-origin-view' },
                id: 'id',
            },
            permission: 'reinc_disease_origin_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/biologicalmonitoring/reinstatements/diseaseOrigin/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar el Tipo de evento __name__'
        },
        permission: 'reinc_disease_origin_d'
        }],
    }],
    configuration: {
        urlData: '/biologicalmonitoring/reinstatements/diseaseOrigin/data',
        filterColumns: true,
    }
},{
  name: 'reinstatements-origin-advisor',
  fields: [
      { name: 'sau_reinc_origin_advisor.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_reinc_origin_advisor.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'reinstatements-origin-advisor-edit' },
                id: 'id',
            },
            permission: 'reinc_origin_advisor_u'
        }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'reinstatements-origin-advisor-view' },
                id: 'id',
            },
            permission: 'reinc_origin_advisor_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/biologicalmonitoring/reinstatements/originAdvisor/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la Procedencia de recomendaciones __name__'
        },
        permission: 'reinc_origin_advisor_d'
        }],
    }],
    configuration: {
        urlData: '/biologicalmonitoring/reinstatements/originAdvisor/data',
        filterColumns: true,
    }
},{
  name: 'reinstatements-labor-conclusions',
  fields: [
      { name: 'sau_reinc_labor_conclusions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_reinc_labor_conclusions.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'reinstatements-labor-conclusions-edit' },
                id: 'id',
            },
            permission: 'reinc_labor_conclusion_u'
        }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'reinstatements-labor-conclusions-view' },
                id: 'id',
            },
            permission: 'reinc_labor_conclusion_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/biologicalmonitoring/reinstatements/laborConclusion/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la Conclusión laboral __name__'
        },
        permission: 'reinc_labor_conclusion_d'
        }],
    }],
    configuration: {
        urlData: '/biologicalmonitoring/reinstatements/laborConclusion/data',
        filterColumns: true,
    }
},{
  name: 'reinstatements-medical-conclusions',
  fields: [
      { name: 'sau_reinc_medical_conclusions.id', data: 'id', title: 'ID', sortable: false, searchable: false, detail: false, key: true },
      { name: 'sau_reinc_medical_conclusions.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'reinstatements-medical-conclusions-edit' },
                id: 'id',
            },
            permission: 'reinc_medical_conclusion_u'
        }, {
            config: {
                color: 'outline-info',
                borderless: true,
                icon: 'ion ion-md-eye',
                title: 'Ver'
            },
            data: {
                routePush: { name: 'reinstatements-medical-conclusions-view' },
                id: 'id',
            },
            permission: 'reinc_medical_conclusion_r'
        }]
    },
    {
        type: 'base',
        buttons: [{
        name: 'delete',
        data: {
            action: '/biologicalmonitoring/reinstatements/medicalConclusion/',
            id: 'id',
            messageConfirmation: 'Esta seguro de borrar la Conclusión médica __name__'
        },
        permission: 'reinc_medical_conclusion_d'
        }],
    }],
    configuration: {
        urlData: '/biologicalmonitoring/reinstatements/medicalConclusion/data',
        filterColumns: true,
    }
},
];
