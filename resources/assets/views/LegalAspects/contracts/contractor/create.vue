<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="CREAR CONTRATISTA"
			url="legalaspects-contractor"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-contract 
						url="/legalAspects/contracts"
						method="POST"
						:roles="roles"
						:contract-classifications="contractClassifications"
						:cancel-url="{ name: 'legalaspects-contractor'}"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormContract from '@/components/LegalAspects/Contracts/ContractLessee/FormContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'legalaspects-contractor-create',
	metaInfo: {
		title: 'Contratistas - Crear'
	},
	components:{
		FormContract
	},
	data(){
		return {
			roles: [],
			contractClassifications: []
		}
	},
	created(){
		this.fetchSelect('roles', '/selects/ctRoles')
		this.fetchSelect('contractClassifications', '/selects/ctContractClassifications')
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
