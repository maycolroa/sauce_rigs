<template>
  <div>
    <header-module
      		title="SEGURIDAD VIAL"
			subtitle="VER VEHICULOS - COMBUSTIBLE"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <combustible-form
              :url="`/industrialSecurity/roadsafety/vehiclesCombustible/${this.$route.params.id}`"
              :combustible="data"
              :view-only="true"
			  :vehicle="data.vehicle_id"
              :cancel-url="{ name: 'industrialsecure-roadsafety-vehicles-combustible'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import CombustibleForm from '@/components/IndustrialSecure/RoadSafety/Vehicles/FormCombustibleComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	name: 'industrialsecure-roadsafety-vehicles-combustible-view',
	metaInfo: {
		title: 'Vehiculos - Combustible - Ver'
	},
	components:{
		CombustibleForm
	},
	data(){
		return {
      		data: [],
	  	}
	},
	created(){
		axios.get(`/industrialSecurity/roadsafety/vehiclesCombustible/${this.$route.params.id}`)
		.then(response => {
			this.data = response.data.data;
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>