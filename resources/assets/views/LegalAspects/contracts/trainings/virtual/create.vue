<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR CAPACITACIÓN VIRTUAL"
      url="legalaspects-contracts-trainings-virtual"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-training-component
                url="/legalAspects/trainingContract"
                method="POST"
                :cancel-url="{ name: 'legalaspects-contracts-trainings-virtual'}"                
                activitiesUrl="/selects/contracts/ctActivitiesAll"
                typeQuestionUrl="/selects/contracts/ctTrainingTypeQuestions"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTrainingComponent from '@/components/LegalAspects/Contracts/Trainings/FormTrainingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-trainings-virtual-create',
  metaInfo: {
    title: 'Capacitaciones Virtuales - Crear'
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
