<template>
  <div>
    <header-module
      v-if="!modal"
      title="ADMINNISTRATIVO"
      subtitle="CREAR PROCESO"
      url="administrative-processes"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-process-form 
                url="/administration/process"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                :cancel-url="{ name: 'administrative-processes'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>   

<script>
import AdministrativeProcessForm from '@/components/Administrative/Processes/FormProcessComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-processes-create',
  metaInfo: {
    title: 'Procesos - Crear'
  },
  components:{
    AdministrativeProcessForm
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
