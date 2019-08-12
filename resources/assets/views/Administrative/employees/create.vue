<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Empleados /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <loading :display="!ready"/>
            <div v-if="ready">
              <template v-if="form == 'default'">
                <form-employee
                  url="/administration/employee"
                  method="POST"
                  :sexs="sexs"
                  regionals-data-url="/selects/regionals"
                  headquarters-data-url="/selects/headquarters"
                  areas-data-url="/selects/areas"
                  processes-data-url="/selects/processes"
                  positions-data-url="/selects/positions"
                  businesses-data-url="/selects/businesses"
                  eps-data-url="/selects/eps"
                  :cancel-url="{ name: 'administrative-employees'}"/>
              </template>
              <template v-if="form == 'vivaAir'">
                <form-employee-viva-air 
                  url="/administration/employee"
                  method="POST"
                  :sexs="sexs"
                  regionals-data-url="/selects/regionals"
                  headquarters-data-url="/selects/headquarters"
                  areas-data-url="/selects/areas"
                  processes-data-url="/selects/processes"
                  positions-data-url="/selects/positions"
                  businesses-data-url="/selects/businesses"
                  eps-data-url="/selects/eps"
                  afp-data-url="/selects/afp"
                  :cancel-url="{ name: 'administrative-employees'}"/>
              </template>
              <template v-if="form == 'misionEmpresarial'">
                <form-employee-empresarial
                  url="/administration/employee"
                  method="POST"
                  :sexs="sexs"
                  :contract-types="contractTypes"
                  regionals-data-url="/selects/regionals"
                  headquarters-data-url="/selects/headquarters"
                  areas-data-url="/selects/areas"
                  processes-data-url="/selects/processes"
                  positions-data-url="/selects/positions"
                  businesses-data-url="/selects/businesses"
                  eps-data-url="/selects/eps"
                  afp-data-url="/selects/afp"
                  arl-data-url="/selects/arl"
                  :cancel-url="{ name: 'administrative-employees'}"/>
              </template>
            </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEmployee from '@/components/Administrative/Employees/FormEmployeeComponent.vue';
import FormEmployeeVivaAir from '@/components/Administrative/Employees/FormEmployeeVivaAirComponent.vue';
import FormEmployeeEmpresarial from '@/components/Administrative/Employees/FormEmployeeEmpresarialComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'administrative-employees-create',
  metaInfo: {
    title: 'Empleados - Crear'
  },
  components:{
    FormEmployee,
    FormEmployeeVivaAir,
    FormEmployeeEmpresarial,
    Loading
  },
  data(){
    return {
      sexs: [],
      contractTypes: [],
      ready: false,
      form: ''
    }
  },
  created(){
    axios.post(`/configurableForm/formModel`, {key: 'form_employee'})
		.then(response => {
      this.form = response.data;
      
      if (this.form == 'misionEmpresarial')
      {
        axios.post(`/configurableForm/selectOptions`, {key: 'employee_select_contract_types'})
        .then(response => {
          this.contractTypes = response.data;
        })
        .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
        });
      }

			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
    });
    
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
