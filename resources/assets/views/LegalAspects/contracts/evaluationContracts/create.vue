<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="EVALUAR"
        url="legalaspects-evaluations"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-contract-component
                :url="`/legalAspects/evaluationContract`"
                method="POST"
                :evaluation="data"
                :types-rating="typesRating"
                userDataUrl="/selects/users"
                :cancel-url="{ name: 'legalaspects-evaluations'}"
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
  name: 'legalaspects-evaluations-contratc-create',
  metaInfo: {
    title: 'Evaluaciones - Evaluar'
  },
  components:{
    FormEvaluationContractComponent
  },
  data () {
    return {
      data: [],
      typesRating: [],
      actionPlanStates: []
    }
  },
  created(){

    axios.get(`/legalAspects/evaluationContract/getData/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;

        if (this.data.evaluation.in_edit)
        {
          Alerts.error('Error', 'Estimado usuario en este momento la evaluación se encuentra en edición por el usuario' + ' ' + this.data.evaluation.user_edit);
          this.$router.go(-1);
        }
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });

    this.fetchSelect('typesRating', '/legalAspects/typeRating/AllTypesRating')
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
        });
    },
  }
}
</script>