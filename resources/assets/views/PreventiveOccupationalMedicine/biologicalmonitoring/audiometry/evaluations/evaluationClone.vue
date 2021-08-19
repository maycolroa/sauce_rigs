<template>
  <div>
    <header-module
			title="AUDIOMETRIAS EVALUACIONES"
			subtitle="CREAR EVALUACIÓN"
			url="audiometry-evaluations"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <form-evaluation-perform
                :url="`/biologicalmonitoring/audiometry/evaluationPerform`"
                method="POST"
                :evaluation="data"
                userDataUrl="/selects/users"
                :cancel-url="{ name: 'audiometry-evaluations'}"
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
import FormEvaluationPerform from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Evaluations/EvaluationPerform/FormEvaluationPerformComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'audiometry-evaluations-perform-create',
  metaInfo: {
    title: 'Evaluaciones - Evaluar'
  },
  components:{
    FormEvaluationPerform
  },
  data(){
    return {
      data: [],
      typesRating: [],
      actionPlanStates: []
    }
  },
  created(){
     axios.get(`/biologicalmonitoring/audiometry/evaluationPerform/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        console.log(this.data)
        delete this.data.id
        this.data.evaluation.stages.map((objective) => {
          objective.criterion.map((subobjective) => {
            subobjective.items.map((item) => {
              item.file = [];
              item.actionPlan.activities.map((plan) => {
                plan.id = ''

                return plan;
              });
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
        });
    },
  }
}
</script>
