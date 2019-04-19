<template>
  	<div>
		<h4 class="font-weight-bold mb-4">
			<span class="text-muted font-weight-light">Contratistas /</span> Completando información
		</h4>


		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-contract-complete-info 
						:url="`/legalAspects/contracts/${this.$route.params.id}`"
						method="PUT"
						:cancel-url="{ name: 'legalaspects-contracts'}"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormContractCompleteInfo from '@/components/LegalAspects/Contracts/ContractLessee/FormContractCompleteInfoComponent.vue';

export default {
	name: 'legal-aspects-contracts-complete-information',
	metaInfo: {
		title: 'Contratistas - Completar información'
	},
	components:{
		FormContractCompleteInfo
	}
}
</script>