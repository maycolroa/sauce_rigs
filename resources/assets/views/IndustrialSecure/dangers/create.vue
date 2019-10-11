<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGRO"
      subtitle="CREAR PELIGRO"
      url="industrialsecure-dangers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-danger-form
                url="/industrialSecurity/danger"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangers'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureDangerForm from '@/components/IndustrialSecure/Dangers/FormDangerComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-dangers-create',
  metaInfo: {
    title: 'Peligros - Crear'
  },
  components:{
    IndustrialSecureDangerForm
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
