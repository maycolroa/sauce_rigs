<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <biological-monitoring-audiometry-form
                url="/biologicalmonitoring/audiometry"
                method="POST"
                employees-data-url="/selects/employees"
                :epp="epp"
                :exposition-level="expositionLevel"
                :cancel-url="{ name: 'biologicalmonitoring-audiometry'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import BiologicalMonitoringAudiometryForm from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/FormAudiometryComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'audiometry-create',
  metaInfo: {
    title: 'Audiometria - Crear'
  },
  components:{
    BiologicalMonitoringAudiometryForm
  },
  data(){
    return {
      epp:[],
      expositionLevel: []
    }
  },
  created(){
    GlobalMethods.getConfigMultiselect('biologicalmonitoring_audiometries_select_epp')
    .then(response => {
        this.epp = response;
    })
    .catch(error => {
        this.$router.go(-1);
    });

    GlobalMethods.getConfigMultiselect('biologicalmonitoring_audiometries_select_exposition_level')
    .then(response => {
        this.expositionLevel = response;
    })
    .catch(error => {
        this.$router.go(-1);
    });
  }
}
</script>
