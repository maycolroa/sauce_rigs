<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`CREAR ${keywordCheck('position')}`"
      url="administrative-positions"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 500px">
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
  metaInfo() {
    return {
      title: `${this.keywordCheck('positions')} - Crear`
    }
  },
  components:{
    AdministrativePositionForm
  }
}
</script>
