<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="CREAR UBICACIÓN"
      url="industrialsecure-epps-locations"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <location-form
                url="/industrialSecurity/epp/location"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-epps-locations'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import LocationForm from '@/components/IndustrialSecure/Epp/FormLocationComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-locations-create',
  metaInfo: {
    title: 'Ubicaciones - Crear'
  },
  components:{
    LocationForm
  },
  data(){
    return {
    }
  }
}
</script>
