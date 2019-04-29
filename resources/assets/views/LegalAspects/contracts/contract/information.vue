<template>
  	<div>
		<h4 class="font-weight-bold mb-4">
			<span class="text-muted font-weight-light">Contratistas /</span> Información
		</h4>


		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<loading :display="!ready"/>
					<form-contract-information
						v-if="ready"
						:url="`/legalAspects/contracts/${contract_id}`"
						method="PUT"
						:contract="data"
						:kindsRisks="kindsRisks"
						:cancel-url="{ name: 'legalaspects-contracts'}"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormContractInformation from '@/components/LegalAspects/Contracts/ContractLessee/FormContractInformationComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'legalaspects-contracts-information',
	metaInfo: {
		title: 'Contratistas - Información'
	},
	components:{
		FormContractInformation,
		Loading
	},
	data () {
		return {
			data: [],
			kindsRisks: [],
			ready: false,
			contract_id: 0
		}
	},
	created(){
		axios.get(`/legalAspects/contracts/getInformation`)
		.then(response => {
			this.data = response.data.data;
			this.contract_id = this.data.id
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});

		this.fetchSelect('kindsRisks', '/selects/ctkindsRisks') 
	},
	methods: {
		fetchSelect(key, url)
		{
			GlobalMethods.getDataMultiselect(url)
			.then(response => {
				this[key] = response;
			})
			.catch(error => {
				Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
				this.$router.go(-1);
			});
		},
	}
}
</script>