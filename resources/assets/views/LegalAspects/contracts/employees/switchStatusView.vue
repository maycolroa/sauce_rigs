<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="VER ESTADO"
      url="legalaspects-contracts-employees"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1200px">
            <form-employee-swith
              url="/legalAspects/employeeContract/switchStatus"
              :employee="data"
              :cancel-url="{ name: 'legalaspects-contracts-employees'}"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEmployeeSwith from '@/components/LegalAspects/Contracts/Employees/FormCheckSwithViewComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contracts-employees-swich-state',
  metaInfo() {
    return {
      title: 'Ver Estado'
    }
  },
  components:{
    FormEmployeeSwith
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/legalAspects/employeeContract/${this.$route.params.id}`)
      .then(response => {
          this.data = response.data.data;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
  }
}
</script>
