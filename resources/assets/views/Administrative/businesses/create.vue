<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="CREAR CENTRO DE COSTOS"
      url="administrative-businesses"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-business-form 
                url="/administration/business"
                method="POST"
                :cancel-url="{ name: 'administrative-businesses'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeBusinessForm from '@/components/Administrative/Businesses/FormBusinessComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-businesses-create',
  metaInfo: {
    title: 'Cetro de Costos - Crear'
  },
  components:{
    AdministrativeBusinessForm
  },
  data(){
    return {
    }
  }
}
</script>
