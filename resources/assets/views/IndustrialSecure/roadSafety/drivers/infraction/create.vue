<template>
  	<div>
		<header-module
      		title="SEGURIDAD VIAL"
			subtitle="CREAR CONDUCTORES - INFRACCIONES"
			url="industrialsecure-roadsafety-drivers"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<infraction-form 
						url="/industrialSecurity/roadsafety/driverInfractions"
						method="POST"
            			:action-plan-states="actionPlanStates"
						:cancel-url="{ name: 'industrialsecure-roadsafety-drivers'}"
						:driver="`${this.$route.params.id}`"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import InfractionForm from '@/components/IndustrialSecure/RoadSafety/Drivers/FormInfractionsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'industrialsecure-roadsafety-drivers-infraction-create',
	metaInfo: {
		title: 'Conductores - infracciones - Crear'
	},
	components:{
		InfractionForm
	},
	data(){
		return {
      		actionPlanStates: []
		}
	},
	created(){
    	this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
	},
	methods: {
		fetchSelect(key, url)
		{
			GlobalMethods.getDataMultiselect(url)
			.then(response => {
				this[key] = response;
			})
			.catch(error => {
				Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
				this.$router.go(-1);
			});
		},
	}
}
</script>
