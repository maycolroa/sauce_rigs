<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="CREAR EVALUACIÓN"
			url="audiometry-evaluations"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-component
                url="/biologicalmonitoring/audiometry/evaluation"
                method="POST"
                :cancel-url="{ name: 'audiometry-evaluations'}"
                :module-id="3"
                :evaluation="data"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEvaluationComponent from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Evaluations/FormEvaluationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'audiometry-evaluations-create',
  metaInfo: {
    title: 'Evaluaciones - Crear'
  },
  components:{
    FormEvaluationComponent
  },
  data(){
    return {
      data: []
    }
  },
  created(){

    axios.get(`/biologicalmonitoring/audiometry/evaluation/${this.$route.params.id}`)
    .then(response => {
        console.log(response.data.data)
        this.data = response.data.data;
        delete this.data.id
        this.data.stages.map((objective) => {
          delete objective.id
          delete objective.evaluation_id

          objective.criterion.map((subobjective) => {
            delete subobjective.id
            delete subobjective.evaluation_stage_id

            subobjective.items.map((item) => {
              delete item.id
              delete item.evaluation_criterion_id

              return item;
            });

            return subobjective;
          });

          return objective;
        });
    })
  }
}
</script>
