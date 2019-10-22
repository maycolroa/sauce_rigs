<template>
  <div>
    <header-module
      v-if="!modal"
      title="SISTEMA"
      subtitle="CREAR COMPAÑIA"
      url="system-companies"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-company
                url="/system/company"
                method="POST"
                :cancel-url="{ name: 'system-companies'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCompany from '@/components/System/Companies/FormCompanyComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-companies-create',
  metaInfo: {
    title: 'Compañias - Crear'
  },
  components:{
    FormCompany
  },
  props: {
    modal: { type: Boolean, default: false },
  },
  data(){
    return {
    }
  }
}
</script>
