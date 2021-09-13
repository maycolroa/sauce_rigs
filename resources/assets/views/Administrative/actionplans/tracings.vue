<template>
  	<div>
		<header-module
			title="PLANES DE ACCIÓN"
			subtitle="SEGUIMIENTOS"
			url="administrative-actionplans"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-tracing-component
						url="/administration/actionplan/saveTracing"
						method="POST"
						:cancel-url="{ name: 'administrative-actionplans'}"
						:tracings="tracings"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormTracingComponent from '@/components/Administrative/ActionPlans/FormActionPlansTracingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	name: 'administrative-actionplans-tracings',
	metaInfo: {
		title: 'Planes de acción - Seguimientos'
	},
	components:{
		FormTracingComponent
	},
	data(){
		return {
			tracings: {
				tracings: [],
				activity_id: '',
				isEdit: false
			}
		}
	},
	created(){
		//axios para obtener los documentos
		axios.post("/administration/actionplan/getTracings", {id: `${this.$route.params.id}`})
		.then(response => {
			this.tracings = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
		});
	},
}
</script>
