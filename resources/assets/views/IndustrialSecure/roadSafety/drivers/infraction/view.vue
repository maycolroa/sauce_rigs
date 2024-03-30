<template>
  <div>
    <header-module
		title="SEGURIDAD VIAL"
		subtitle="VER CONDUCTORES - INFRACCIONES"
	/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
			<infraction-form 
              :url="`/industrialSecurity/roadsafety/driverInfractions/${this.$route.params.id}`"
              :infraction="data"
              :view-only="true"
              :action-plan-states="actionPlanStates"
			  :driver="data.driver_id"
              :cancel-url="{ name: 'industrialsecure-roadsafety-drivers-infraction'}"/>
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
	name: 'industrialsecure-roadsafety-drivers-infraction-view',
	metaInfo: {
		title: 'Conductores - infracciones - Ver'
	},
	components:{
		InfractionForm
	},
	data(){
		return {
      		data: [],
      		actionPlanStates: []
	  	}
	},
	created() {

    	this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')

		axios.get(`/industrialSecurity/roadsafety/driverInfractions/${this.$route.params.id}`)
		.then(response => {
			this.data = response.data.data;
			console.log(this.data)
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
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