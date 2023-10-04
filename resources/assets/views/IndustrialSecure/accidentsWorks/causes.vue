<template>
  	<div>
		<header-module
			title="ACCIDENTES E INCIDENTES DE TRABAJO"
			subtitle="CAUSAS"
			url="industrialsecure-accidentswork"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
          			<loading :display="!ready"/>
					<div v-if="ready">
						<form-causes-component
							url="/industrialSecurity/accidents/saveCauses"
							method="POST"
							:cancel-url="{ name: 'industrialsecure-accidentswork'}"
							:causes="causes"/>
					</div>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormCausesComponent from '@/components/IndustrialSecure/AccidentsWork/FormCausesComponent.vue';
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
	name: 'industrialsecure-accidentswork-causes',
	metaInfo: {
		title: 'Accidentes e incidentes - Causas'
	},
	components:{
		FormCausesComponent,
		Loading
	},
	data(){
		return {
			causes: [				
				{
					key: new Date().getTime(),
					description: 'Causas Inmediatas',
					secondary: []
				},
				{
					key: new Date().getTime(),
					description: 'Causas Básicas/Raíz',
					secondary: []
				}
			],
      		ready: false,
		}
	},
	created(){
		//axios para obtener los documentos
		axios.post("/industrialSecurity/accidents/getCauses", {id: `${this.$route.params.id}`})
		.then(response => {
			//this.causes = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
		});
	},
}
</script>
