<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="ASIGNACIÓN MASIVA DE RESPONSABLES"
			url="legalaspects-contractor"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-masive-responsibles-contract
						url="/legalAspects/contracts/saveMasiveResponsibles"
						method="POST"
						:cancel-url="{ name: 'legalaspects-contractor'}"
						:data="data"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormMasiveResponsiblesContract from '@/components/LegalAspects/Contracts/ContractLessee/FormMasiveResponsiblesContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'legalaspects-contracts-masive-responsibles',
	metaInfo: {
		title: 'Contratistas - Configurar Masivamente Responsables'
	},
	components:{
		FormMasiveResponsiblesContract
	},
	data(){
		return {
			data: {
				contracts: [],
				responsibles: []
			}
		}
	},
	created(){
		//axios para obtener los documentos
		axios.post("/legalAspects/contracts/getInformationResponsibles")
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
