<template>
  <div>
    <header-module
      title="AUSENTISMO"
      :subtitle="auth.can['absen_reports_admin_user'] && !auth.can['absen_reports_c'] ? 'EDITAR USUARIOS' : 'EDITAR INFORME'"
      url="absenteeism-reports"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <report-form
                :url="`/biologicalmonitoring/absenteeism/report/${this.$route.params.id}`"
                 method="PUT"
                :report="data"
                :is-edit="true"
                :cancel-url="{ name: 'absenteeism-reports'}"
                :is-add="auth.can['absen_reports_admin_user'] && !auth.can['absen_reports_c']"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import ReportForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/Reports/FormReportComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-reports-user-add',
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