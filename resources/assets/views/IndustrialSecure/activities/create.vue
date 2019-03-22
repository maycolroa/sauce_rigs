<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Procesos /</span> Crear
    </h4>


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
    title: 'Procesos - Crear'
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
