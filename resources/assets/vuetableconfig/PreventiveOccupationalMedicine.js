export default [
    {
    name: 'biologicalmonitoring-audiometry',
    fields : [
        {name : 'bm_audiometries.id', data:'id', title : 'ID',sortable: false, searchable : false, detail : false, key : true},
        {name : 'bm_audiometries.date', data:'date', title : 'Fecha',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.air_right_pta', data:'air_right_pta', title : 'Aereo derecho PTA',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.air_right_pta', data:'air_left_pta', title : 'Aereo izquierdo PTA',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.severity_grade_air_right_pta', data:'severity_grade_air_right_pta', title : 'Grado de severidad aereo derecho PTA',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.severity_grade_air_left_pta', data:'severity_grade_air_left_pta', title : 'Grado de severidad aereo izquierdo PTA',sortable: true, searchable : true, detail : false, key : false},        
        {name : 'bm_audiometries.gap_right', data:'gap_right', title : 'GAP Derecho',sortable: true, searchable : true, detail : false, key : false},        
        {name : 'bm_audiometries.gap_left', data:'gap_left', title : 'GAP Izquierdo',sortable: true, searchable : true, detail : false, key : false},        
        {name : 'sau_employees.identification', data:'identification', title : 'Identificación empleado',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_employees.name', data:'name', title : 'Nombre empleado',sortable: true, searchable : true, detail : false, key : false},
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
          routePush: {name: 'biologicalmonitoring-audiometry-edit'},
          id: 'id',
        }
      },{
        config: {
          color:'outline-info',
          borderless: true,
          icon:'ion ion-md-eye',
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-view'},
          id: 'id',
        }
      },{
        config: {
          color:'outline-success',
          borderless: true,
          icon:'fas fa-chart-line',
        },
        data:{
          routePush: {name: 'biologicalmonitoring-audiometry-report'},
          id: 'id',
        }
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
      }],
    }],
    configuration: {
        urlData: '/biologicalmonitoring/audiometry/data',
        filterColumns: true,
        detailComponent: '/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/DetailVuetableAudiometryComponent.vue'
    }
}

];
