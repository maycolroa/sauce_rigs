<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="CREAR CONCLUSIÓN MÉDICA"
      url="reinstatements-medical-conclusions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-medical-conclusion-component
                url="/biologicalmonitoring/reinstatements/medicalConclusion"
                method="POST"
                :cancel-url="{ name: 'reinstatements-medical-conclusions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormMedicalConclusionComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/MedicalConclusions/FormMedicalConclusionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-medical-conclusions-create',
  metaInfo: {
    title: 'Conclusión médica - Crear'
  },
  components:{
    FormMedicalConclusionComponent
  },
  data(){
    return {
    }
  }
}
</script>
