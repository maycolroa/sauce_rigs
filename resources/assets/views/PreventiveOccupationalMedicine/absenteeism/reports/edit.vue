<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Informes /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <report-form
                :url="`/biologicalmonitoring/absenteeism/report/${this.$route.params.id}`"
                 method="PUT"
                :report="data"
                :is-edit="true"
                :cancel-url="{ name: 'absenteeism-reports'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import ReportForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/Reports/FormReportComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-reports-edit',
  metaInfo: {
    title: 'Informes - Editar'
  },
  components:{
    ReportForm
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
  },
}
</script>