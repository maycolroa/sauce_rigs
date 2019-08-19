<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Cetro de Costos /</span> Crear
    </h4>


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
