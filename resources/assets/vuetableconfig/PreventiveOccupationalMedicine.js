export default [
    {
    name: 'biologicalmonitoring-audiometry',
    fields : [
        {name : 'bm_audiometries.id', data:'id', title : 'ID',sortable: false, searchable : false, detail : false, key : true},
        {name : 'bm_audiometries.date', data:'date', title : 'Fecha',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.type', data:'type', title : 'Tipo',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.work_zone_noise', data:'work_zone_noise', title : 'Ruido de la zona de trabajo',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.previews_events', data:'previews_events', title : 'Eventos Previos',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.exposition_level', data:'exposition_level', title : 'Nivel de exposicion (Dosimetría)',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_clasification', data:'left_clasification', title : 'Clasificación izquierda',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.right_clasification', data:'right_clasification', title : 'Clasificación derecha',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.test_score', data:'test_score', title : 'Resultado de la prueba',sortable: true, searchable : true, detail : false, key : false},
        {name : 'bm_audiometries.epp', data:'epp', title : 'EPP',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_500', data:'left_500', title : 'Izquierda 500',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_1000', data:'left_1000', title : 'Izquierda 1000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_2000', data:'left_2000', title : 'Izquierda 2000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_3000', data:'left_3000', title : 'Izquierda 3000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_4000', data:'left_4000', title : 'Izquierda 4000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_6000', data:'left_6000', title : 'Izquierda 6000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.left_8000', data:'left_8000', title : 'Izquierda 8000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.right_500', data:'right_500', title : 'Derecha 500',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.right_1000', data:'right_1000', title : 'Derecha 1000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.right_2000', data:'right_2000', title : 'Derecha 2000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.right_3000', data:'right_3000', title : 'Derecha 3000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.right_4000', data:'right_4000', title : 'Derecha 4000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.right_6000', data:'right_6000', title : 'Derecha 6000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.right_8000', data:'right_8000', title : 'Derecha 8000',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.recommendations', data:'recommendations', title : 'Recomendaciones',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.observation', data:'observation', title : 'Observacion',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.created_at', data:'created_at', title : 'Fecha Creacion',sortable: false, searchable : false, detail : true, key : false},
        {name : 'bm_audiometries.updated_at', data:'updated_at', title : 'Fecha Actualizacion',sortable: false, searchable : false, detail : true, key : false},
        {name : 'sau_employees.identification', data:'employee_identification', title : 'Identificación empleado',sortable: true, searchable : true, detail : false, key : false},
        {name : 'sau_employees.name', data:'employee_name', title : 'Nombre empleado',sortable: true, searchable : true, detail : false, key : false},
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
    }
}

];
