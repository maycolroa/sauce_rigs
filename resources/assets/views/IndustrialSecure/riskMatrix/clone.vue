<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="EDITAR MATRIZ"
      url="industrialsecure-riskmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-danger-matrix-form
                url="/industrialSecurity/dangersMatrix"
                method="POST"
                :dangerMatrix="data"
                :cancel-url="{ name: 'industrialsecure-riskmatrix'}"
                :type-activities="typeActivities"
                :danger-generated="dangerGenerated"
                :si-no="siNo"
                :qualifications="qualifications"
                :action-plan-states="actionPlanStates"
                userDataUrl="/selects/users"
                :configuration="configuration"
                :fields="fields"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import IndustrialSecureDangerMatrixForm from '@/components/IndustrialSecure/DangerMatrix/FormDangerMatrixComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-riskmatrix-clone',
  metaInfo: {
    title: 'Matriz de Riesgos - Clonar'
  },
  components:{
    IndustrialSecureDangerMatrixForm
  },
  data () {
    return {
      typeActivities: [],
      dangerGenerated: [],
      siNo: [],
      qualifications: [],
      actionPlanStates: [],
      data: [],
      configuration: [],
      fields: {}
    }
  },
  created(){
    axios.get('/administration/configuration/view')
    .then(response => {
        this.configuration = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    axios.get(`/industrialSecurity/dangersMatrix/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.fields = response.data.data.add_fields;
        delete this.data.id
        this.data.activities.map((activity) => {
          activity.id = ''
          delete activity.danger_matrix_id

          activity.dangers.map((danger) => {
            danger.id = ''
            delete danger.dm_activity_id

              danger.qualifications.map((qualify) => {
                delete qualify.activity_danger_id

                return qualify;
              });

              danger.actionPlan.activities.map((plan) => {
                plan.id = ''

                return plan;
              });

            return danger;
          });

          return activity;
        });
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    this.fetchSelect('typeActivities', '/radios/dmTypeActivities')
    this.fetchSelect('dangerGenerated', '/selects/dmGeneratedDangers')
    this.fetchSelect('siNo', '/radios/siNo')
    this.fetchSelect('qualifications', '/industrialSecurity/dangersMatrix/getQualificationsComponent')
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