<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="LISTA DE ESTÁNDARES MÍNIMOS"
			url="legalaspects-contractor"
		/>


		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<loading v-if="!ready" :display="!ready"/>
						<list-check-items-validations
							v-else
							url="/legalAspects/contracts/saveValidations"
							method="POST"
							:items="items"				
							:cancel-url="{ name: 'legalaspects-contractor'}"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import ListCheckItemsValidations from '@/components/LegalAspects/Contracts/ContractLessee/FormValidationListCheckItemsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
	name: 'legalaspects-contracts-list-check-items',
	metaInfo: {
		title: 'Contratistas - Validación lista de items'
	},
	components:{
		ListCheckItemsValidations,
		Loading
	},
	data () {
		return {
			items: {},
			ready: false,
		}
	},
	created(){
		//axios para obtener los items a calificar
		axios.post("/legalAspects/contracts/getItemValidation")
		.then(response => {
			this.items = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar los items, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>