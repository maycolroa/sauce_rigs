<template>
  <div>
    <header-module
      		title="SEGURIDAD VIAL"
			subtitle="VER VEHICULOS - MANTENIMIENTO"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <maintenance-form
              :url="`/industrialSecurity/roadsafety/vehiclesMaintenance/${this.$route.params.id}`"
              :maintenance="data"
			  :vehicle="data.vehicle_id"
              :view-only="true"
              :cancel-url="{ name: 'industrialsecure-roadsafety-vehicles-maintenance'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import MaintenanceForm from '@/components/IndustrialSecure/RoadSafety/Vehicles/FormMaintenanceComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	name: 'industrialsecure-roadsafety-vehicles-maintenance-view',
	metaInfo: {
		title: 'Vehiculos - Mantenimiento - Ver'
	},
	components:{
		MaintenanceForm
	},
	data(){
		return {
      		data: [],
	  	}
	},
	created(){
		axios.get(`/industrialSecurity/roadsafety/vehiclesMaintenance/${this.$route.params.id}`)
		.then(response => {
			this.data = response.data.data;
			console.log(this.data.vehicle_id)
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>