<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="VER EMPLEADO"
      url="legalaspects-contracts-employees"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-contract-employee
                :employee="data"
                :view-only="true"
                :sexs="sexs"
                activitiesUrl="/selects/contracts/ctActivitiesContracts"
                afp-data-url="/selects/afp"
                :cancel-url="{ name: 'legalaspects-contracts-employees'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormContractEmployee from '@/components/LegalAspects/Contracts/Employees/FormContractEmployeeComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-contracts-employees-view',
  metaInfo: {
    title: 'Empleados - Ver'
  },
  components:{
    FormContractEmployee
  },
  data () {
    return {
      data: [],
			sexs: [],
    }
  },
  created(){
    axios.get(`/legalAspects/employeeContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    	  this.fetchSelect('sexs', '/selects/sexs')
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        //this.$router.go(-1);
    });
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
				//this.$router.go(-1);
			});
		},
	}
}
</script>