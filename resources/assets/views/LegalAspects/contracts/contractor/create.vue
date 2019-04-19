<template>
  	<div>
		<h4 class="font-weight-bold mb-4">
			<span class="text-muted font-weight-light">Contratistas /</span> Crear
		</h4>


		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-contract 
						url="/legalAspects/contracts"
						method="POST"
						:cancel-url="{ name: 'legalaspects-contracts'}"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormContract from '@/components/LegalAspects/Contracts/ContractLessee/FormContractComponent.vue';

export default {
	name: 'legal-aspects-contracts-create',
	metaInfo: {
		title: 'Contratistas - Crear'
	},
	components:{
		FormContract
	}
}
</script>
