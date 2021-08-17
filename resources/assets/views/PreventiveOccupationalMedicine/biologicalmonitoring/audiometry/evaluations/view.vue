<template>
  <div>
    <header-module
			title="AUDIOMETRIAS"
			subtitle="VER EVALUACIÓN"
			url="audiometry-evaluations"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-evaluation-component
                :evaluation="data"
                :view-only="true"
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
  name: 'biologicalmonitoring-audiometry-view',
  metaInfo: {
    title: 'Evaluaciones - Ver'
  },
  components:{
    FormEvaluationComponent
  },
  data () {
    return {
      data: []
    }
  },
  created(){

    axios.get(`/biologicalmonitoring/audiometry/evaluation/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
}
</script>