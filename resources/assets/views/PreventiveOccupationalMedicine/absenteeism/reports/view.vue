<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="VER INFORME"
      url="absenteeism-reports"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 450px; padding-right: 10px;">
            <iframe :src="data.url" class="col-md-12" height="800" width="1200" frameborder="0" id="iframeInforme"></iframe>
          </perfect-scrollbar>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar'

export default {
  name: 'absenteeism-reports-view',
  metaInfo: {
    title: 'Informes - Ver'
  },
  components: {
    PerfectScrollbar
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/absenteeism/report/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    axios.post(`/biologicalmonitoring/absenteeism/report/monitorView/${this.$route.params.id}`)
    .then(response => {
    })
    .catch(error => {
    });
  },
}
</script>