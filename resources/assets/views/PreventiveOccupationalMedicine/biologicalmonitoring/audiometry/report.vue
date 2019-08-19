<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Reporte
    </h4>
        <div class="row">

          <div class="col-lg">
            <b-card border-variant="primary" title="Derecha Aereo" class="mb-3 box-shadow-none">
              <chart-line-audiometry 
                :data="dataAirRight"
                :data-axis="dataAirAxis"
                title="Derecha"
                color-line="blue"/>
            </b-card>
          </div>
          <div class="col-lg">

            <b-card border-variant="secondary" title="Izquierda Aereo" class="mb-3 box-shadow-none">
              <chart-line-audiometry 
                :data="dataAirLeft"
                :data-axis="dataAirAxis"
                title="Izquierda"
                color-line="red"/>
            </b-card>
          </div>
         
        </div>
        <div class="row">

          <div class="col-lg">
            <b-card border-variant="primary" title="Derecha Oseo" class="mb-3 box-shadow-none">
              <chart-line-audiometry 
                :data="dataOsseousRight"
                :data-axis="dataOsseousAxis"
                title="Derecha"
                color-line="blue"/>
            </b-card>
          </div>
          <div class="col-lg">

            <b-card border-variant="secondary" title="Izquierda Oseo" class="mb-3 box-shadow-none">
              <chart-line-audiometry 
                :data="dataOsseousLeft"
                :data-axis="dataOsseousAxis"
                title="Izquierda"
                color-line="red"/>
            </b-card>
          </div>
         
        </div>
         <b-btn variant="default" :to="{name: 'biologicalmonitoring-audiometry'}">Atras</b-btn>
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
      dataAirLeft: [],
      dataAirRight: [],
      dataOsseousLeft: [],
      dataOsseousRight: [],
      dataAirAxis : ['500','1000','2000','3000','4000','6000','8000'],
      dataOsseousAxis : ['500','1000','2000','3000','4000'],
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/audiometry/${this.$route.params.id}`)
    .then(response => {
        this.dataAirLeft.push(response.data.data.air_left_500,
                          response.data.data.air_left_1000,
                          response.data.data.air_left_2000,
                          response.data.data.air_left_3000,
                          response.data.data.air_left_4000,
                          response.data.data.air_left_6000,
                          response.data.data.air_left_8000);

          this.dataAirRight.push(response.data.data.air_right_500,
                          response.data.data.air_right_1000,
                          response.data.data.air_right_2000,
                          response.data.data.air_right_3000,
                          response.data.data.air_right_4000,
                          response.data.data.air_right_6000,
                          response.data.data.air_right_8000);

          this.dataOsseousLeft.push(response.data.data.osseous_left_500,
                          response.data.data.osseous_left_1000,
                          response.data.data.osseous_left_2000,
                          response.data.data.osseous_left_3000,
                          response.data.data.osseous_left_4000);

          this.dataOsseousRight.push(response.data.data.osseous_right_500,
                          response.data.data.osseous_right_1000,
                          response.data.data.osseous_right_2000,
                          response.data.data.osseous_right_3000,
                          response.data.data.osseous_right_4000);
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>