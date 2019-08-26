<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Informes /</span> Crear
    </h4>


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
