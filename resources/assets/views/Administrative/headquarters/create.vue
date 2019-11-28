<template>
  <div>
    <header-module
      v-if="!modal"
      title="ADMINISTRATIVO"
      :subtitle="`CREAR ${keywordCheck('headquarter')}`"
      url="administrative-headquarters"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-headquarter-form 
                url="/administration/headquarter"
                method="POST"
                regionals-data-url="/selects/regionals"
                :cancel-url="{ name: 'administrative-headquarters'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeHeadquarterForm from '@/components/Administrative/Headquarters/FormHeadquarterComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-positions-create',
  metaInfo() {
    return {
      title: `${this.keywordCheck('headquarters')} - Crear`
    }
  },
  components:{
    AdministrativeHeadquarterForm
  },
  props: {
    modal: { type: Boolean, default: false },
  }
}
</script>
