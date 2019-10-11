<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="CALIFICAR REPORTE"
      url="dangerousconditions-reports"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection-qualification
                :url="`/industrialSecurity/dangerousConditions/report/${this.$route.params.id}`"
                method="PUT"
                :report="data"
                :action-plan-states="actionPlanStates"
                :is-edit="true"
                :cancel-url="{ name: 'dangerousconditions-reports'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInspectionQualification from '@/components/IndustrialSecure/DangerousConditions/Reports/FormInspectionQualificationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'dangerousconditions-reports-qualification-view',
  metaInfo: {
    title: 'Reportes - Calificar'
  },
  components:{
    FormInspectionQualification
  },
  data () {
    return {
      data: [],
      actionPlanStates: []
    }
  },
  created(){
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
    
    axios.get(`/industrialSecurity/dangerousConditions/report/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
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