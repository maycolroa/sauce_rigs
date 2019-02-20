<template>
  	<div>
		<h4 class="font-weight-bold mb-4">
		<span class="text-muted font-weight-light">Usuarios /</span> Contratistas
		</h4>


		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<contract-lessee-form 
						url="/legalAspects/contracts"
						method="POST"
						roles-data-url="/selects/roles"
						:cancel-url="{ name: 'legalaspects-contracts'}"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import ContractLesseeForm from '@/components/LegalAspects/ContractLessee/FormContractComponent.vue';

export default {
	name: 'legal-aspects-contracts-create',
	metaInfo: {
		title: 'Usuarios - Crear'
	},
	components:{
		ContractLesseeForm
	}
}
</script>
