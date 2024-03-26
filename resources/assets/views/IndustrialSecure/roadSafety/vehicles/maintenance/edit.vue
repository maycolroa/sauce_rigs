<template>
  	<div>
		<header-module
      		title="SEGURIDAD VIAL"
			subtitle="EDITAR VEHICULOS - MANTENIMIENTO"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body style="height: 800px">
					<maintenance-form
						:url="`/industrialSecurity/roadsafety/vehiclesMaintenance/${this.$route.params.id}`"
						method="PUT"
						:maintenance="data"
						:vehicle="data.vehicle_id"
                		:is-edit="true"
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
	name: 'industrialsecure-roadsafety-vehicles-maintenance-edit',
	metaInfo: {
		title: 'Vehiculos - Mantenimiento - Editar'
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
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>
