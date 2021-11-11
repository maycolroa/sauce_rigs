<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="LISTA DE ESTÁNDARES MÍNIMOS"
			url="legalaspects-list-check-qualification"
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
							:url="`/legalAspects/listCheck/saveQualificationItems`"
							method="POST"
							:contractId="contractId"
							:items="items"
							:qualifications="qualifications"
							:qualificationListId="qualificationListId"		
							:validate_qualificacion="validate_qualificacion"			
							:cancel-url="{ name: 'legalaspects-list-check-qualification'}"/>
					</template>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import ListCheckItems from '@/components/LegalAspects/Contracts/ListCheck/FormListCheckItemsComponent.vue';
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
			contractId : '',
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
		axios.post("/legalAspects/listCheck/getValidationQualification")
		.then(response => {
			this.validate_qualificacion = response.data.data;
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar las calificaciones, por favor contacte con el administrador');
		});

		//axios para obtener los items a calificar
		axios.post("/legalAspects/listCheck/getListCheckItems", { id: this.$route.params.id })
		.then(response => {
			this.items = response.data.data;
			this.contractId = response.data.data.id;
			this.qualificationListId = response.data.data.id_qualification_list;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar los items, por favor contacte con el administrador');
		});
	},
}
</script>