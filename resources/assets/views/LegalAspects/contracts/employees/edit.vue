<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="EDITAR EMPLEADO"
      url="legalaspects-contracts-employees"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <form-contract-employee
                :url="`/legalAspects/employeeContract/${this.$route.params.id}`"
                method="PUT"
                :employee="data"
                :sexs="sexs"
                activitiesUrl="/selects/contracts/ctActivitiesContracts"
                afp-data-url="/selects/afp"
                :is-edit="true"
                :cancel-url="{ name: 'legalaspects-contracts-employees'}"
                :states="states"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormContractEmployee from '@/components/LegalAspects/Contracts/Employees/FormContractEmployeeComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'legalaspects-contracts-employees-edit',
  metaInfo: {
    title: 'Empleados - Editar'
  },
  components:{
    FormContractEmployee,
    Loading
  },
  data () {
    return {
      data: [],
			sexs: [],
      ready: false,
      states: [],
    }
  },
  created(){
    axios.get(`/legalAspects/employeeContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    	  this.fetchSelect('sexs', '/selects/sexs')
        this.fetchSelect('states', '/selects/contracts/statesFile')
        setTimeout(() => {
            this.ready = true
        }, 1000)
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