<template>
  <div>
    <header-module
      title="CAPACITACIONES"
      subtitle="CREAR CAPACITACIÓN"
      url="industrialsecure-roadsafety-trainings"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-training-component
                url="/industrialSecurity/roadsafety/training"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-roadsafety-trainings'}"    
                typeQuestionUrl="/selects/rsTrainingTypeQuestions"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTrainingComponent from '@/components/IndustrialSecure/RoadSafety/Trainings/FormTrainingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'roadSafety-trainings-create',
  metaInfo: {
    title: 'Capacitaciones - Crear'
  },
  components:{
    FormTrainingComponent
  },
  props: {
  },
  data(){
    return {
    }
  }
}
</script>
