<template>
  <div>
    <header-module
      v-if="!modal"
      title="ADMINISTRATIVO"
      :subtitle="`CREAR ${keywordCheck('area')}`"
      url="administrative-areas"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-area-form 
                url="/administration/area"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                processes-data-url="/selects/processes"
                :cancel-url="{ name: 'administrative-areas'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeAreaForm from '@/components/Administrative/Areas/FormAreaComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-areas-create',
  metaInfo() {
    return {
      title: `${this.keywordCheck('areas')} - Crear`
    }
  },
  components:{
    AdministrativeAreaForm
  },
  props: {
    modal: { type: Boolean, default: false },
  }
}
</script>
