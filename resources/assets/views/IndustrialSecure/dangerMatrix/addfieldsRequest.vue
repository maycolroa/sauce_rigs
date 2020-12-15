<template>
  	<div>
		<header-module
			title="MATRIZ DE PELIGROS"
			subtitle="CREAR MATRIZ"
			url="industrialsecure-dangermatrix"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-additional-fields
						url="/legalAspects/contracts/saveDocuments"
						method="POST"
						:cancel-url="{ name: 'industrialsecure-dangermatrix'}"
						:fields="fields"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormAdditionalFields from '@/components/IndustrialSecure/DangerMatrix/FormAdditionalFieldsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'danger-matrix-add-fields',
	metaInfo: {
		title: 'Matríz de Peligro - Campos Adiccionales'
	},
	components:{
		FormAdditionalFields
	},
	data(){
		return {
			fields: {
				fields: []
			}
		}
	},
	created(){
		//axios para obtener los documentos
		axios.post("/legalAspects/contracts/getfields")
		.then(response => {
			this.fields = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>
