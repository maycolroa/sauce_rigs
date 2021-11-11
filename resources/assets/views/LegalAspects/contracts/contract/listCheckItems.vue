<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="LISTA DE ESTÁNDARES MÍNIMOS"
			:url="(this.$route.params.id ? 'legalaspects-contractor' : 'legalaspects-contracts')"
		/>


		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<loading v-if="!ready" :display="!ready"/>
					<template v-else>
						<template v-if="items.length == 0">
							<p class="mb-0">No hay estándares definidos para usted</p>
						</template>
						<list-check-items
							v-else
							:url="`/legalAspects/contracts/saveQualificationItems`"
							method="POST"
							:contractId="(this.$route.params.id ? this.$route.params.id : '')"
							:items="items"
							:qualifications="qualifications"	
							:qualificationListId="qualificationListId"	
							:validate_qualificacion="validate_qualificacion"		
							:cancel-url="{ name: (this.$route.params.id ? 'legalaspects-contractor' : 'legalaspects-contracts')}"/>
					</template>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import ListCheckItems from '@/components/LegalAspects/Contracts/ContractLessee/FormListCheckItemsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
	name: 'legalaspects-contracts-list-check-items',
	metaInfo: {
		title: 'Contratistas - Lista de items'
	},
	components:{
		ListCheckItems,
		Loading
	},
	data () {
		return {
			items: [],
			qualifications: [],
			ready: false,
			qualificationListId: '',
			validate_qualificacion: ''
		}
	},
	created(){
		//axios para obtener las calificaciones
		axios.get("/legalAspects/contracts/qualifications")
		.then(response => {
			this.qualifications = response.data;
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar las calificaciones, por favor contacte con el administrador');
		});

		//Obtener validacion de la empresa para las calificaciones de la lista de chequeo
		axios.post("/legalAspects/contracts/getValidationQualificarion")
		.then(response => {
			console.log(response)
			//this.validate_qualificacion = response.data;
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar las calificaciones, por favor contacte con el administrador');
		});

		//axios para obtener los items a calificar
		axios.post("/legalAspects/contracts/getListCheckItems", { id: this.$route.params.id })
		.then(response => {
			this.items = response.data.data;
			this.qualificationListId = response.data.data.id_qualification_list;
			this.ready = true
		})
		.catch(error => {
			if (error.response.status == 500 && error.response.data.error != 'Internal Error')
			{
				Alerts.error('Error', error.response.data.error);
			}
			else
			{
				Alerts.error('Error', 'Se ha generado un error en el proceso al cargar los items, por favor contacte con el administrador');
			}
		});
	},
}
</script>