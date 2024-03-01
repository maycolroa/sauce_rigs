<template>
  	<div>
		<header-module
      		title="SEGURIDAD VIAL"
			subtitle="CREAR VEHICULOS - MANTENIMIENTO"
			url="industrialsecure-roadsafety-vehicles"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<maintenance-form
						url="/industrialSecurity/roadsafety/vehiclesMaintenance"
						method="POST"
						:cancel-url="{ name: 'industrialsecure-roadsafety-vehicles'}"
						:vehicle="`${this.$route.params.id}`"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import MaintenanceForm from '@/components/IndustrialSecure/RoadSafety/Vehicles/FormMaintenanceComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	name: 'industrialsecure-roadsafety-vehicles-maintenance-create',
	metaInfo: {
		title: 'Vehiculos - Mantenimiento - Crear'
	},
	components:{
		MaintenanceForm
	},
	data(){
		return {}
	},
	created(){},
}
</script>
