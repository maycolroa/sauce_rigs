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
                  		:sexs="sexs"
						:cancel-url="{ name: 'legalaspects-contracts-employees'}"
						activitiesUrl="/selects/contracts/ctActivitiesContracts"
                  		afp-data-url="/selects/afp"/>
				</b-card-body>
			</b-card>
		</div>  	</div>
</template>

<script>
import FormContractEmployee from '@/components/LegalAspects/Contracts/Employees/FormContractEmployeeComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'legalaspects-contracts-employee-create',
	metaInfo: {
		title: 'Empleados - Crear'
	},
	components:{
		FormContractEmployee
	},
	data(){
		return {
			sexs: [],
		}
	},
	created() {    
    	this.fetchSelect('sexs', '/selects/sexs')
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
