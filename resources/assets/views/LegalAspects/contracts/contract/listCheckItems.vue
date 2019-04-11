<template>
  	<div>
		<h4 class="font-weight-bold mb-4">
			<span class="text-muted font-weight-light">Contratistas /</span> Lista de estándares mínimos
		</h4>


		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<list-check-items
						url="/legalAspects/contracts/saveQualificationItems"
						method="POST"
						:contract="contract"
						:name="name"
						:qualifications="qualifications"
                		:view-only="false"					
						:cancel-url="{ name: 'legalaspects-contracts'}"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import ListCheckItems from '@/components/LegalAspects/ContractLessee/ListCheckItemsComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	name: 'legalaspects-contracts-list-check-items',
	metaInfo: {
		title: 'Contratistas - Lista de items'
	},
	components:{
		ListCheckItems
	},
	data () {
		return {
			contract: {items: []},
			qualifications: {},
			name: ''
		}
	},
	created(){
		//axios para obtener los items a calificar
		axios.get("/legalAspects/contracts/data")
		.then(response => {
			this.contract.items = response.data;
			this.name = response.data[0].name
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar los items, por favor contacte con el administrador');
			this.$router.go(-1);
		});
		//axios para obtener las calificaciones
		axios.get("/legalAspects/contracts/qualifications")
		.then(response => {
			// let qualifications = response.data;
			// array.forEach(qualifications => {
				
			// });
			this.qualifications = response.data;
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso al cargar las calificaciones, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
}
</script>