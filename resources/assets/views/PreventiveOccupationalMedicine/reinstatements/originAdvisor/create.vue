<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="CREAR PROCEDENCIA DE RECOMENDACIONES"
      url="reinstatements-origin-advisor"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-origin-advisor-component
                url="/biologicalmonitoring/reinstatements/originAdvisor"
                method="POST"
                :cancel-url="{ name: 'reinstatements-origin-advisor'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormOriginAdvisorComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/OriginAdvisor/FormOriginAdvisorComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-origin-advisor-create',
  metaInfo: {
    title: 'Procedencia de recomendaciones - Crear'
  },
  components:{
    FormOriginAdvisorComponent
  },
  data(){
    return {
    }
  }
}
</script>
