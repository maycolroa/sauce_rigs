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
      { name: 'sau_bm_musculoskeletal_analysis.document_type', data: 'document_type', title: 'Tipo Identificación', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.patient_identification', data: 'patient_identification', title: 'Identificación Paciente', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.name', data: 'name', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.professional_identification', data: 'professional_identification', title: 'Identificación Profesional', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.professional', data: 'professional', title: 'Profesional', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.order', data: 'order', title: 'Orden', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.date', data: 'date', title: 'Fecha', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.attention_code', data: 'attention_code', title: 'Código Atención', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.attention', data: 'attention', title: 'Atención', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.evaluation_type', data: 'evaluation_type', title: 'Tipo de Evaluación', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.evaluation_format', data: 'evaluation_format', title: 'Formato Evaluación', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.department', data: 'department', title: 'Departamento', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.nit_company', data: 'nit_company', title: 'Nit Empresa', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.company', data: 'company', title: 'Empresa', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.nit_company_mission', data: 'nit_company_mission', title: 'Nit Empresa en Misión', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.company_mission', data: 'company_mission', title: 'Empresa en Misión', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.branch_office', data: 'branch_office', title: 'Sucursal', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.sex', data: 'sex', title: 'Sexo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.age', data: 'age', title: 'Edad', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.etareo_group', data: 'etareo_group', title: 'Grupo Etáreo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.phone', data: 'phone', title: 'Teléfono', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.phone_alternative', data: 'phone_alternative', title: 'Teléfono Alterno', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.eps', data: 'eps', title: 'EPS', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.afp', data: 'afp', title: 'AFP', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.stratum', data: 'stratum', title: 'Estrato', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.number_people_charge', data: 'number_people_charge', title: 'Número de personas a cargo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.scholarship', data: 'scholarship', title: 'Escolaridad', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.marital_status', data: 'marital_status', title: 'Estado Civil', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.position', data: 'position', title: 'Cargo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.antiquity', data: 'antiquity', title: 'Antigüedad', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.ant_atep_ep', data: 'ant_atep_ep', title: 'Ant. ATEP-EP', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.which_ant_atep_ep', data: 'which_ant_atep_ep', title: 'Cuales Ant. ATEP-EP', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.exercise_habit', data: 'exercise_habit', title: 'Habito Ejercicio', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.exercise_frequency', data: 'exercise_frequency', title: 'Frecuencia Ejercicio', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.liquor_habit', data: 'liquor_habit', title: 'Habito Licor', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.liquor_frequency', data: 'liquor_frequency', title: 'Frecuencia Licor', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.exbebedor_habit', data: 'exbebedor_habit', title: 'Habito Exbebedor', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.liquor_suspension_time', data: 'liquor_suspension_time', title: 'Tiempo Suspensión Licor', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.cigarette_habit', data: 'cigarette_habit', title: 'Habito Cigarrillo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.cigarette_frequency', data: 'cigarette_frequency', title: 'Frecuencia Cigarrillo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.habit_extra_smoker', data: 'habit_extra_smoker', title: 'Habito Exfumador', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.cigarrillo_suspension_time', data: 'cigarrillo_suspension_time', title: 'Tiempo Suspensión Cigarrillo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.activity_extra_labor', data: 'activity_extra_labor', title: 'Actividad Extralaboral', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.pressure_systolic', data: 'pressure_systolic', title: 'Presión  Sistólica', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.pressure_diastolic', data: 'pressure_diastolic', title: 'Presión Diastólica', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.weight', data: 'weight', title: 'Peso', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.size', data: 'size', title: 'Talla', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.imc', data: 'imc', title: 'IMC', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.imc_lassification', data: 'Clasificación IMC', title: 'Nombre', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.abdominal_perimeter', data: 'abdominal_perimeter', title: 'Perímetro Abdominal', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.abdominal_perimeter_classification', data: 'abdominal_perimeter_classification', title: 'Clasificación Perímetro Abdominal', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_1', data: 'diagnostic_code_1', title: 'Código Diagnóstico 1', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_1', data: 'diagnostic_1', title: 'Diagnóstico 1', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_2', data: 'diagnostic_code_2', title: 'Código Diagnóstico 2', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_2', data: 'diagnostic_2', title: 'Diagnóstico 2', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_3', data: 'diagnostic_code_3', title: 'Código Diagnóstico 3', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_3', data: 'diagnostic_3', title: 'Diagnóstico 3', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_4', data: 'diagnostic_code_4', title: 'Código Diagnóstico 4', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_4', data: 'diagnostic_4', title: 'Diagnóstico 4', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_5', data: 'diagnostic_code_5', title: 'Código Diagnóstico 5', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_5', data: 'diagnostic_5', title: 'Diagnóstico 5', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_6', data: 'diagnostic_code_6', title: 'Código Diagnóstico 6', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_6', data: 'diagnostic_6', title: 'Diagnóstico 6', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_7', data: 'diagnostic_code_7', title: 'Código Diagnóstico 7', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_7', data: 'diagnostic_7', title: 'Diagnóstico 7', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_8', data: 'diagnostic_code_8', title: 'Código Diagnóstico 8', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_8', data: 'diagnostic_8', title: 'Diagnóstico 8', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_9', data: 'diagnostic_code_9', title: 'Código Diagnóstico 9', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_9', data: 'diagnostic_9', title: 'Diagnóstico 9', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_10', data: 'diagnostic_code_10', title: 'Código Diagnóstico 10', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_10', data: 'diagnostic_10', title: 'Diagnóstico 10', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_11', data: 'diagnostic_code_11', title: 'Código Diagnóstico 11', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_11', data: 'diagnostic_11', title: 'Diagnóstico 11', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_12', data: 'diagnostic_code_12', title: 'Código Diagnóstico 12', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_12', data: 'diagnostic_12', title: 'Diagnóstico 12', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_13', data: 'diagnostic_code_13', title: 'Código Diagnóstico 13', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_13', data: 'diagnostic_13', title: 'Diagnóstico 13', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_14', data: 'diagnostic_code_14', title: 'Código Diagnóstico 14', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_14', data: 'diagnostic_14', title: 'Diagnóstico 14', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_15', data: 'diagnostic_code_15', title: 'Código Diagnóstico 15', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_15', data: 'diagnostic_15', title: 'Diagnóstico 15', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_16', data: 'diagnostic_code_16', title: 'Código Diagnóstico 16', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_16', data: 'diagnostic_16', title: 'Diagnóstico 16', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_17', data: 'diagnostic_code_17', title: 'Código Diagnóstico 17', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_17', data: 'diagnostic_17', title: 'Diagnóstico 17', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_code_18', data: 'diagnostic_code_18', title: 'Código Diagnóstico 18', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.diagnostic_18', data: 'diagnostic_18', title: 'Diagnóstico 18', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.cardiovascular_risk', data: 'cardiovascular_risk', title: 'Riesgo Cardiovascular', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.osteomuscular_classification', data: 'osteomuscular_classification', title: 'Clasificación Osteomuscular', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.osteomuscular_group', data: 'osteomuscular_group', title: 'Grupo Osteomuscular', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.age_risk', data: 'age_risk', title: 'Riesgo Edad (A)', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.pathological_background_risks', data: 'pathological_background_risks', title: 'Riesgos Antecedentes Patológicos (B)', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.extra_labor_activities_risk', data: 'extra_labor_activities_risk', title: 'Riesgo Actividades Extralaborales (C)', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.sedentary_risk', data: 'sedentary_risk', title: 'Riesgo Sedentarismo (D)', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.imc_risk', data: 'imc_risk', title: 'Riesgo IMC (E)', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.consolidated_personal_risk_punctuation', data: 'consolidated_personal_risk_punctuation', title: 'Consolidado Riesgo Personal (Puntuación)', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.consolidated_personal_risk_criterion', data: 'consolidated_personal_risk_criterion', title: 'Consolidado Riesgo Personal (Criterio)', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.prioritization_medical_criteria', data: 'prioritization_medical_criteria', title: 'Criterio Médico de Priorización', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.concept', data: 'concept', title: 'Concepto', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.recommendations', data: 'recommendations', title: 'Recomendaciones', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.observations', data: 'observations', title: 'Observaciones', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.restrictions', data: 'restrictions', title: 'Restricciones', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.remission', data: 'remission', title: 'Remisión', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.authorization_access_information', data: 'authorization_access_information', title: 'Autorización Acceso a la Información', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.date_end', data: 'date_end', title: 'Fecha Fin', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.description_medical_exam', data: 'description_medical_exam', title: 'Descripción Examen Médico', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.symptom', data: 'symptom', title: 'Síntomas', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.symptom_type', data: 'symptom_type', title: 'Tipo Sintoma', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.body_part', data: 'body_part', title: 'Parde del cuerpo', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.periodicity', data: 'periodicity', title: 'Periodicidad', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.workday', data: 'workday', title: 'Jornada Laboral', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.symptomatology_observations', data: 'symptomatology_observations', title: 'Observaciones', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.optometry', data: 'optometry', title: 'Optometría', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.visiometry', data: 'visiometry', title: 'Visiometría', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.audiometry', data: 'audiometry', title: 'Audiometría', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.spirometry', data: 'spirometry', title: 'Espirometría', sortable: true, searchable: true, detail: false, key: false },
      { name: 'sau_bm_musculoskeletal_analysis.tracing', data: 'tracing', title: 'Seguimiento', sortable: true, searchable: true, detail: false, key: false }
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
      { name: 'sau_absen_file_upload.created_at', data: 'created_at', title: 'Fecha de creacion', sortable: true, searchable: true, detail: false, key: false },
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
                routePush: { name: 'absenteeism-upload-files-edit' },
                id: 'id',
            },
            permission: 'absen_uploadFiles_u'
        }
      ]
  },
  {
      type: 'base',
      buttons: [{
      name: 'delete',
      data: {
          action: '/biologicalmonitoring/absenteeism/fileUpload/',
          id: 'id',
          messageConfirmation: 'Esta seguro de borrar el archivo __name__'
      },
      permission: 'absen_uploadFiles_d'
      }],
  }],
  configuration: {
      urlData: '/biologicalmonitoring/absenteeism/fileUpload/data',
      filterColumns: true,
  }
}
];
