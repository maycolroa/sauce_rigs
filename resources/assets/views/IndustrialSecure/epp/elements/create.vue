<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="CREAR ELEMENTO"
      url="industrialsecure-epps-elements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <element-form
                url="/industrialSecurity/epp/element"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-epps-elements'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import ElementForm from '@/components/IndustrialSecure/Epp/FormElementComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-elements-create',
  metaInfo: {
    title: 'Elementos - Crear'
  },
  components:{
    ElementForm
  },
  data(){
    return {
    }
  }
}
</script>
