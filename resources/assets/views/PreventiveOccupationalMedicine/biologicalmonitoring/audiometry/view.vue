<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <biological-monitoring-audiometry-form
                :audiometry="data"
                :view-only="true"
                :cancel-url="{ name: 'biologicalmonitoring-audiometry'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import BiologicalMonitoringAudiometryForm from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/FormAudiometryComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'audiometry-view',
  metaInfo: {
    title: 'Audiometria - Ver'
  },
  components:{
    BiologicalMonitoringAudiometryForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/audiometry/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>