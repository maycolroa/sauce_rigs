<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="CREAR REPORTE"
      url="dangerousconditions-reports"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-report
                url="/industrialSecurity/dangerousConditions/report"
                method="POST"
                conditions-data-url="/selects/industrialSecurity/conditions"
                :rates="rates"                
                :action-plan-states="actionPlanStates"
                :cancel-url="{ name: 'dangerousconditions-reports' }"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormReport from '@/components/IndustrialSecure/DangerousConditions/Reports/FormReportComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'dangerousconditions-reports-create',
  metaInfo: {
    title: 'Reportes - Crear'
  },
  components:{
    FormReport
  },
  data () {
    return {
        rates: [],
        actionPlanStates: []
    }
  },
  created(){
      this.fetchSelect('rates', '/selects/industrialSecurity/rates')
      this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
  },
  methods: {
      fetchSelect(key, url)
      {
          GlobalMethods.getDataMultiselect(url)
          .then(response => {
              this[key] = response;
          })
          .catch(error => {
              Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
              this.$router.go(-1);
          });
      },
  }
}
</script>
