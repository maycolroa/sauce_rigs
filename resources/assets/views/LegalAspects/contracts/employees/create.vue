<template>
  	<div>
		<header-module
			title="CONTRATISTAS"
			subtitle="CREAR EMPLEADO"
			url="legalaspects-contracts-employees"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<form-contract-employee
						url="/legalAspects/employeeContract"
						method="POST"
						:cancel-url="{ name: 'legalaspects-contracts-employees'}"
						activitiesUrl="/selects/contracts/ctActivitiesContracts"/>
				</b-card-body>
			</b-card>
		</div>  	</div>
</template>

<script>
import FormContractEmployee from '@/components/LegalAspects/Contracts/Employees/FormContractEmployeeComponent.vue';

export default {
	name: 'legalaspects-contracts-employee-create',
	metaInfo: {
		title: 'Empleados - Crear'
	},
	components:{
		FormContractEmployee
	}
}
</script>
