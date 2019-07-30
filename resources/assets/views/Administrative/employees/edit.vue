<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Empleados /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <template v-if="form == 'default'">
              <administrative-employee-form 
                  :url="`/administration/employee/${this.$route.params.id}`"
                  method="PUT"
                  :disable-wacth-select-in-created="true"
                  :sexs="sexs"
                  regionals-data-url="/selects/regionals"
                  headquarters-data-url="/selects/headquarters"
                  areas-data-url="/selects/areas"
                  processes-data-url="/selects/processes"
                  positions-data-url="/selects/positions"
                  businesses-data-url="/selects/businesses"
                  eps-data-url="/selects/eps"
                  :employee="data"
                  :is-edit="true"
                  :cancel-url="{ name: 'administrative-employees'}"/>
            </template>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeEmployeeForm from '@/components/Administrative/Employees/FormEmployeeComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'administrative-employees-edit',
  metaInfo: {
    title: 'Empleados - Editar'
  },
  components:{
    AdministrativeEmployeeForm,
    Loading
  },
  data () {
    return {
      data: [],
      sexs: [],
      ready: false,
      form: ''
    }
  },
  created(){
    axios.post(`/configurableForm/formModel`, {key: 'form_employee'})
		.then(response => {
			this.form = response.data;
      
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

    GlobalMethods.getDataMultiselect('/selects/sexs')
    .then(response => {
        this.sexs = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    })
  },
}
</script>