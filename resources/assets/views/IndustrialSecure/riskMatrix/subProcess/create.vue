<template>
  <div>
    <header-module
      v-if="!modal"
      title="MATRIZ DE RIESGOS"
      subtitle="CREAR SUBPROCESO"
      url="industrialsecure-subprocesses"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-sub-process-form
                url="/industrialSecurity/subProcess"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-subprocesses'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureSubProcessForm from '@/components/IndustrialSecure/RiskMatrix/SubProcess/FormSubProcessComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-subprocess-create',
  metaInfo: {
    title: 'Subprocesos - Crear'
  },
  components:{
    IndustrialSecureSubProcessForm
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
