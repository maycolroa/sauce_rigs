<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="CREAR REPORTE"
      url="industrialsecure-accidentswork-causes-items"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <accident-form
                url="/industrialSecurity/causes/items"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-accidentswork-causes-items'}"
                />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AccidentForm from '@/components/IndustrialSecure/AccidentsWork/Causes/FormItemComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'accidentsWork-causes-items-create',
  metaInfo: {
    title: 'Causas Items - Crear'
  },
  components:{
    AccidentForm
  },
  data(){
    return {
    }
  }
}
</script>
