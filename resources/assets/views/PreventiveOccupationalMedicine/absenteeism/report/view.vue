<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Reportes /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <preventive-occupational-medicine-absenteeism-report-form
                :report="data"
                :view-only="true"
                :cancel-url="{ name: 'preventiveoccupationalmedicine-absenteeism-reports'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import PreventiveOccupationalMedicineAbsenteeismReportForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/Reports/FormReportComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'preventiveoccupationalmedicine-absenteeism-report-view',
  metaInfo: {
    title: 'Ausentismo - Ver'
  },
  components:{
    PreventiveOccupationalMedicineAbsenteeismReportForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/preventiveOccupationalMedicine/absenteeism/report/${this.$route.params.id}`)
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