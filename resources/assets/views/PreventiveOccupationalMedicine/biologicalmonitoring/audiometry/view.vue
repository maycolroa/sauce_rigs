<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <biological-monitoring-audiometry-form
                :epp="epp"
                :audiometry="data"
                :view-only="true"
                :exposition-level="expositionLevel"
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
    this.fetchData()

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
  watch: {
    '$route' (to, from) {
      this.fetchData()
    }
  },
  methods: {
    fetchData() {
      axios.get(`/biologicalmonitoring/audiometry/${this.$route.params.id}`)
        .then(response => {
            this.data = response.data.data;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
    }
  }
}
</script>