<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR ACTIVIDAD"
      url="industrialsecure-activities"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-activity-form
                url="/industrialSecurity/activity"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-activities'}"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureActivityForm from '@/components/IndustrialSecure/Activities/FormActivityComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-activities-create',
  metaInfo: {
    title: 'Actividades - Crear'
  },
  components:{
    IndustrialSecureActivityForm
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
