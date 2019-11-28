<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`VER ${keywordCheck('employee')}`"
      url="administrative-employees"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <template v-if="form == 'default'">
              <form-employee
                  :sexs="sexs"
                  :employee="data"
                  :disable-wacth-select-in-created="true"
                  :view-only="true"
                  :cancel-url="{ name: 'administrative-employees'}"/>
            </template>
            <template v-if="form == 'vivaAir'">
              <form-employee-viva-air 
                  :sexs="sexs"
                  :employee="data"
                  :disable-wacth-select-in-created="true"
                  :view-only="true"
                  :cancel-url="{ name: 'administrative-employees'}"/>
            </template>
            <template v-if="form == 'misionEmpresarial'">
              <form-employee-empresarial
                  :sexs="sexs"
                  :contract-types="contractTypes"
                  :employee="data"
                  :disable-wacth-select-in-created="true"
                  :view-only="true"
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
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'administrative-employees-edit',
  metaInfo() {
    return {
      title: `${this.keywordCheck('employees')} - Ver`
    }
  },
  components:{
    FormEmployee,
    FormEmployeeVivaAir,
    FormEmployeeEmpresarial,
    Loading
  },
  data () {
    return {
      data: [],
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
        .then(response3 => {
          this.contractTypes = response3.data;
        })
        .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
        });
      }
      
      axios.get(`/administration/employee/${this.$route.params.id}`)
      .then(response2 => {
          this.data = response2.data.data;
          this.ready = true
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
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