<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="ASIGNACIÓN MASIVA DE ACTIVIDADES"
			url="legalaspects-contractor"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-masive-activities-contract
						url="/legalAspects/contracts/saveMasiveActivities"
						method="POST"
						:cancel-url="{ name: 'legalaspects-contractor'}"
						:data="data"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormMasiveActivitiesContract from '@/components/LegalAspects/Contracts/ContractLessee/FormMasiveActivitiesContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'legalaspects-contracts-masive-activities',
	metaInfo: {
		title: 'Contratistas - Configurar Masivamente actividades'
	},
	components:{
		FormMasiveActivitiesContract
	},
	data(){
		return {
			data: {
				contracts: [],
				activities: []
			}
		}
	},
	created(){
		//axios para obtener los documentos
		axios.post("/legalAspects/contracts/getInformationActivities")
		.then(response => {
			this.data = response.data.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
		});
	},
}
</script>
