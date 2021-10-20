<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="CREAR EVALUACIÓN"
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

      <b-modal ref="modalBlock" class="modal-slide" hide-header hide-footer @hidden="returnPage()">
        <p class="text-justific mb-4">
          Estimado usuario en este momento la evaluación se encuentra en edición por el usuario <b> {{ data.evaluation ? data.evaluation.user_edit : '' }} </b>
        </p>
        <b-btn block variant="primary" @click="returnPage()">Aceptar</b-btn>
      </b-modal>

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
  data(){
    return {
      data: [],
      typesRating: [],
      actionPlanStates: []
    }
  },
  created(){
     axios.get(`/legalAspects/evaluationContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        console.log(this.data);
        delete this.data.id
        this.data.evaluation.objectives.map((objective) => {
          objective.subobjectives.map((subobjective) => {
            subobjective.items.map((item) => {
              item.file = [];
              item.actionPlan.activities.map((plan) => {
                plan.id = ''

                return plan;
              });
              //item.actionPlan = {"activities":[],"activitiesRemoved":[]};
              item.files_pdf = [];
              item.observations.map((obs) => {
                delete obs.id
                delete obs.evaluation_id

                return obs
              });
              return item;
            });
            return subobjective;
          });
          return objective;
        });

        if (this.data.evaluation.in_edit)
          this.$refs.modalBlock.show();
    })
    .catch(error => {
      console.log(error)
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  methods: {
    returnPage() {
      this.$router.go(-1);
    },
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
