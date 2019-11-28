<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="CREAR CONCLUSIÓN LABORAL"
      url="reinstatements-labor-conclusions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-labor-conclusion-component
                url="/biologicalmonitoring/reinstatements/laborConclusion"
                method="POST"
                :cancel-url="{ name: 'reinstatements-labor-conclusions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLaborConclusionComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/LaborConclusions/FormLaborConclusionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-labor-conclusions-create',
  metaInfo: {
    title: 'Conclusión laboral - Crear'
  },
  components:{
    FormLaborConclusionComponent
  },
  data(){
    return {
    }
  }
}
</script>
