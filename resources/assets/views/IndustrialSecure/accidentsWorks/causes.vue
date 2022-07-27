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
					<form-causes-component
						url="/industrialSecurity/accidents/saveCauses"
						method="POST"
						:cancel-url="{ name: 'industrialsecure-accidentswork'}"
						:causes="causes"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormCausesComponent from '@/components/IndustrialSecure/AccidentsWork/FormCausesComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	name: 'industrialsecure-accidentswork-causes',
	metaInfo: {
		title: 'Accidentes e incidentes - Causas'
	},
	components:{
		FormCausesComponent
	},
	data(){
		return {
			causes: {
				causes: [],
				accident_id: '',
				isEdit: false
			}
		}
	},
	created(){
		//axios para obtener los documentos
		axios.post("/industrialSecurity/accidents/getCauses", {id: `${this.$route.params.id}`})
		.then(response => {
			this.causes = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
		});
	},
}
</script>
