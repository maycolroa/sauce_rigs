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
                :years="years"
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
      years: [],
		}
	},
	created(){
    this.fetchSelect('years', '/selects/legalMatrix/years')
	},
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
            //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            //this.$router.go(-1);
        });
    },
	}
}
</script>
