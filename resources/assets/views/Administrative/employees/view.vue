<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Empleados /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <template v-if="form == 'default'">
              <administrative-employee-form
                  :sexs="sexs"
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
import AdministrativeEmployeeForm from '@/components/Administrative/Employees/FormEmployeeComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'administrative-employees-edit',
  metaInfo: {
    title: 'Empleados - Ver'
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