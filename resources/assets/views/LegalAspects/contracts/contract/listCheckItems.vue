<template>
  	<div>
		<h4 class="font-weight-bold mb-4">
			<span class="text-muted font-weight-light">Contratistas /</span> Lista de estándares mínimos
		</h4>


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
							:items="items"
							:qualifications="qualifications"				
							:cancel-url="{ name: 'legalaspects-contracts'}"/>
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
			this.$router.go(-1);
		});

		//axios para obtener los items a calificar
		axios.get("/legalAspects/contracts/getListCheckItems")
		.then(response => {
			this.items = response.data.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar los items, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>