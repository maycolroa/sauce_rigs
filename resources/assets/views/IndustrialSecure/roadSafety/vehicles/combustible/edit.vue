<template>
  	<div>
		<header-module
      		title="SEGURIDAD VIAL"
			subtitle="EDITAR VEHICULOS - COMBUSTIBLE"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body style="height: 800px">
					<combustible-form
						:url="`/industrialSecurity/roadsafety/vehiclesCombustible/${this.$route.params.id}`"
						method="PUT"
						:combustible="data"
                		:is-edit="true"
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
	name: 'industrialsecure-roadsafety-vehicles-combustible-edit',
	metaInfo: {
		title: 'Vehiculos - Combustible - Editar'
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
