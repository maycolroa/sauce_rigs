<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR CAPACITACIÓN"
      url="legalaspects-contracts-trainings"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-training-component
                url="/legalAspects/trainingContract"
                method="POST"
                :cancel-url="{ name: 'legalaspects-contracts-trainings'}"                
                activitiesUrl="/selects/contracts/ctActivities"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTrainingComponent from '@/components/LegalAspects/Contracts/Trainings/FormTrainingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-trainings-create',
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
