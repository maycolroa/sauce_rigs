<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="CREAR VEHICULO"
      url="industrialsecure-roadsafety-vehicles"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <vehicle-form
                url="/industrialSecurity/roadsafety/vehicles"
                method="POST"
                positions-data-url="/selects/positions"
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
  name: 'industrialsecure-roadsafety-vehicles-create',
  metaInfo: {
    title: 'Vehiculos - Crear'
  },
  components:{
    VehicleForm
  },
  props: {
  },
  data(){
		return {
		}
	},
	created(){
	},
  methods: {
	}
}
</script>
