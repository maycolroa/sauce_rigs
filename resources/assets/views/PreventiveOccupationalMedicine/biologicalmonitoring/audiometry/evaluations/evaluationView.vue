<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="VER EVALUACIÓN REALIZADA"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-contract-component
                :evaluation="data"
                :view-only="true"
                userDataUrl="/selects/users"
                :cancel-url="{ name: 'legalaspects-evaluations-contracts'}"
                :action-plan-states="actionPlanStates"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormEvaluationContractComponent from '@/components/LegalAspects/Contracts/EvaluationContracts/FormEvaluationContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-evaluations-contratcs-view',
  metaInfo: {
    title: 'Evaluaciones Realizadas - Ver'
  },
  components:{
    FormEvaluationContractComponent
  },
  data () {
    return {
      data: [],
      actionPlanStates: []
    }
  },
  created(){

    axios.get(`/biologicalmonitoring/audiometry/evaluation/perform/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

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