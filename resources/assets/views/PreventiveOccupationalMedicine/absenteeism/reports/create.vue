<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="CREAR INFORME"
      url="absenteeism-reports"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <report-form
                url="/biologicalmonitoring/absenteeism/report"
                method="POST"
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
  name: 'absenteeism-reports-create',
  metaInfo: {
    title: 'Informes - Crear'
  },
  components:{
    ReportForm
  },
  data(){
    return {
    }
  }
}
</script>
