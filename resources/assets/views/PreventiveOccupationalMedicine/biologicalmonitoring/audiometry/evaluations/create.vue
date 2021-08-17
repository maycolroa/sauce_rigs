<template>
  <div>
    <header-module
			title="AUDIOMETRIAS"
			subtitle="CREAR EVALUACIÓN"
			url="audiometry-evaluations"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-component
                url="/biologicalmonitoring/audiometry/evaluation"
                method="POST"
                :module-id="3"
                :cancel-url="{ name: 'audiometry-evaluations'}"/>
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
  name: 'biologicalmonitoring-audiometry-create',
  metaInfo: {
    title: 'Evaluaciones - Crear'
  },
  components:{
    FormEvaluationComponent
  },
  data(){
    return {
    }
  }
}
</script>
