<template>
  <div>
    <header-module
        title="AUDIOMETRIAS EVALUACIONES"
        subtitle="EVALUAR"
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
  data () {
    return {
      data: [],
      actionPlanStates: []
    }
  },
  created(){

    axios.get(`/biologicalmonitoring/audiometry/getData/${this.$route.params.id}`)
    .then(response => {
        console.log(response.data.data);
        this.data = response.data.data;

        if (this.data.evaluation.in_edit)
          this.$refs.modalBlock.show();
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });

    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
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