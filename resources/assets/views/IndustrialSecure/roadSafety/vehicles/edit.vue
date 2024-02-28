<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="EDITAR VEHICULO"
      url="industrialsecure-roadsafety-vehicles"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <vehicle-form
                :url="`/industrialSecurity/roadsafety/vehicles/${this.$route.params.id}`"
                method="PUT"
                :vehicles="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-roadsafety-vehicles'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import VehicleForm from '@/components/IndustrialSecure/RoadSafety/Vehicles/FormVehicleComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-roadsafety-vehicles-edit',
  metaInfo: {
    title: 'Vehiculos - Editar'
  },
  components:{
    VehicleForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/roadsafety/vehicles/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  methods: {}
}
</script>