<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="CREAR CARGO"
      url="administrative-positions"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-position-form 
                url="/administration/position"
                method="POST"
                :cancel-url="{ name: 'administrative-positions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativePositionForm from '@/components/Administrative/Positions/FormPositionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-positions-create',
  metaInfo: {
    title: 'Cargos - Crear'
  },
  components:{
    AdministrativePositionForm
  },
  data(){
    return {
    }
  }
}
</script>
