<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Reporte
    </h4>

    <div class="col-md">
          <b-card-group>
         <b-card no-body header="Izquierda" class="text-center mb-3">
            <b-card-body>
               <chart-line-audiometry 
                :data="dataLeft"
                :data-axis="dataAxis"
                title="Izquierda"
                color-line="red"/>
            </b-card-body>
          </b-card>
           <b-card no-body header="Derecha" class="text-center mb-3">
            <b-card-body>
              <chart-line-audiometry 
                :data="dataRight"
                :data-axis="dataAxis"
                title="Derecha"
                color-line="blue"/>
            </b-card-body>
          </b-card>
      </b-card-group>
      <b-btn variant="default" :to="{name: 'biologicalmonitoring-audiometry'}">Atras</b-btn>
    </div>
  </div>
</template>

<script>
import ChartLineAudiometry from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/ChartLineAudiometry.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'audiometry-report',
  metaInfo: {
    title: 'Audiometria - Reporte'
  },
  components:{
    ChartLineAudiometry
  },
  data () {
    return {
      dataLeft: [],
      dataRight: [],
      dataAxis : ['500','1000','2000','3000','4000','6000','8000']
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/audiometry/${this.$route.params.id}`)
    .then(response => {
        this.dataLeft.push(response.data.data.left_500,
                          response.data.data.left_1000,
                          response.data.data.left_2000,
                          response.data.data.left_3000,
                          response.data.data.left_4000,
                          response.data.data.left_6000,
                          response.data.data.left_8000);

          this.dataRight.push(response.data.data.right_500,
                          response.data.data.right_1000,
                          response.data.data.right_2000,
                          response.data.data.right_3000,
                          response.data.data.right_4000,
                          response.data.data.right_6000,
                          response.data.data.right_8000);
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>