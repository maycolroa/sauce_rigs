<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="CREAR TIPO DE EVENTO"
      url="reinstatements-disease-origin"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-disease-origin-component
                url="/biologicalmonitoring/reinstatements/diseaseOrigin"
                method="POST"
                :cancel-url="{ name: 'reinstatements-disease-origin'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormDiseaseOriginComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/DiseaseOrigin/FormDiseaseOriginComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-disease-origin-create',
  metaInfo: {
    title: 'Tipo de evento - Crear'
  },
  components:{
    FormDiseaseOriginComponent
  },
  data(){
    return {
    }
  }
}
</script>
