<template>
  <div>
    <header-module
      v-if="!modal"
      title="ADMINISTRATIVO"
      :subtitle="`CREAR ${keywordCheck('regionals', 'Regionales')}`"
      url="administrative-regionals"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-regional-form 
                url="/administration/regional"
                method="POST"
                :cancel-url="{ name: 'administrative-regionals'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeRegionalForm from '@/components/Administrative/Regionals/FormRegionalComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-regionals-create',
  metaInfo() {
    return {
      title: `${this.keywordCheck('regionals', 'Regionales')} - Crear`
    }
  },
  components:{
    AdministrativeRegionalForm
  },
  props: {
    modal: { type: Boolean, default: false },
  }
}
</script>
