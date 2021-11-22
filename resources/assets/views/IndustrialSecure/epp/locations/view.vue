<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="VER UBICACIÓN"
      url="industrialsecure-epps-locations"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <location-form
                :location="data"
                :view-only="true"
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
  name: 'industrialsecure-epps-locations-view',
  metaInfo: {
    title: 'Ubicaciones - Ver'
  },
  components:{
    LocationForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/epp/location/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>