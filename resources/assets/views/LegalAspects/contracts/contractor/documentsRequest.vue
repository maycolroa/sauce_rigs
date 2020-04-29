<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="SOLICITAR DOCUMENTOS"
			url="legalaspects-contractor"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-contract-documents
						url="/legalAspects/contracts/saveDocuments"
						method="POST"
						:cancel-url="{ name: 'legalaspects-contractor'}"
						:documents="documents"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormContractDocuments from '@/components/LegalAspects/Contracts/ContractLessee/FormContractDocumentsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'legalaspects-contracts-documents',
	metaInfo: {
		title: 'Contratistas - Solicitar documentos'
	},
	components:{
		FormContractDocuments
	},
	data(){
		return {
			documents: {
				documents: []
			}
		}
	},
	created(){
		//axios para obtener los documentos
		axios.post("/legalAspects/contracts/getDocuments")
		.then(response => {
			this.documents = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>
