<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Informes
    </h4>
        <div class="row" style="padding-bottom: 10px;">
          <div class="col-md">
            <b-card no-body>
              <b-card-body>
                <biological-monitoring-audiometry-report-pta-form
                  url="/biologicalmonitoring/audiometry/reportPta"
                  method="POST"
                  :areas="areas"
                  :regionales="regionales"
                  :years="years"/>
              </b-card-body>
            </b-card>
          </div>
        </div>
        <div class="row">
          <div class="col-lg">
            <b-card border-variant="primary" title="Derecha Aéreo PTA" class="mb-3 box-shadow-none">
              <chart-pie-audiometry 
                :data="dataAirRightPta"
                :legend="legendAirRightPta"
                title="Derecha"
                color-line="blue"/>
            </b-card>
          </div>
          <div class="col-lg">

            <b-card border-variant="secondary" title="Izquierda Aéreo PTA" class="mb-3 box-shadow-none">
              <chart-pie-audiometry 
                :data="dataAirLeftPta"
                :legend="legendAirLeftPta"
                title="Izquierda"
                color-line="red"/>
            </b-card>
          </div>
         
        </div>
         <b-btn variant="default" :to="{name: 'biologicalmonitoring-audiometry'}">Atras</b-btn>
  </div>
</template>

<script>
import ChartPieAudiometry from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/ChartPieAudiometry.vue';
import BiologicalMonitoringAudiometryReportPtaForm from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/FormAudiometryReportPtaComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'audiometry-report-pta',
  metaInfo: {
    title: 'Audiometria - Informes'
  },
  components:{
    ChartPieAudiometry,
    BiologicalMonitoringAudiometryReportPtaForm
  },
  data () {
    return {
        dataAirLeftPta: [],
        dataAirRightPta: [],
        legendAirLeftPta: [],
        legendAirRightPta: [],
        areas: [],
        regionales: [],
        years: []
    }
  },
  created(){
    GlobalMethods.getDataMultiselect('/selects/areas')
    .then(response => {
        this.areas = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    GlobalMethods.getDataMultiselect('/selects/regionals')
    .then(response => {
        this.regionales = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    GlobalMethods.getDataMultiselect('/selects/years/audiometry')
    .then(response => {
        this.years = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    this.$on('changeDataReportPta', data => {
      this.legendAirLeftPta = data.air_left_legend
      this.dataAirLeftPta = data.air_left_pta
      
      this.legendAirRightPta = data.air_right_legend
      this.dataAirRightPta = data.air_right_pta
    });
  }
}
</script>