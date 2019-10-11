<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="CREAR INSPECCIÓN"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection
                url="/industrialSecurity/dangerousConditions/inspection"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                areas-data-url="/selects/areas"
                processes-data-url="/selects/processes"
                :cancel-url="{ name: 'dangerousconditions-inspections' }"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInspection from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormInspectionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousconditions-inspections-create',
  metaInfo: {
    title: 'Inspecciones - Crear'
  },
  components:{
    FormInspection
  },
  data(){
    return {
    }
  }
}
</script>
