<template>
  <div>
    <header-module
      title="AUDIOMETRIAS"
      subtitle="EDITAR AUDIOMETRIA"
      url="biologicalmonitoring-audiometry"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <biological-monitoring-audiometry-form
                :url="`/biologicalmonitoring/audiometry/${this.$route.params.id}`"
                method="PUT"
                :epp="epp"
                :audiometry="data"
                :exposition-level="expositionLevel"
                :is-edit="true"
                employees-data-url="/selects/employees"
                :cancel-url="{ name: 'biologicalmonitoring-audiometry'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import BiologicalMonitoringAudiometryForm from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/FormAudiometryComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js'

export default {
  name: 'audiometry-edit',
  metaInfo: {
    title: 'Audiometria - Editar'
  },
  components:{
    BiologicalMonitoringAudiometryForm
  },
  data () {
    return {
      data: [],
      epp:[],
      expositionLevel: []
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
  },
}
</script>