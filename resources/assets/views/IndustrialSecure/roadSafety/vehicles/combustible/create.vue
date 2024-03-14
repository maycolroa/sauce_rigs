<template>
  	<div>
		<header-module
      		title="SEGURIDAD VIAL"
			subtitle="CREAR VEHICULOS - COMBUSTIBLE"
			url="industrialsecure-roadsafety-vehicles"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<combustible-form 
						url="/industrialSecurity/roadsafety/vehiclesCombustible"
						method="POST"
						:cancel-url="{ name: 'industrialsecure-roadsafety-vehicles'}"
						:vehicle="`${this.$route.params.id}`"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import CombustibleForm from '@/components/IndustrialSecure/RoadSafety/Vehicles/FormCombustibleComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	name: 'industrialsecure-roadsafety-vehicles-combustible-create',
	metaInfo: {
		title: 'Vehiculos - Mantenimiento - Crear'
	},
	components:{
		CombustibleForm
	},
	data(){
		return {}
	},
	created(){},
}
</script>
