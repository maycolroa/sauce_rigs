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
                :cancel-url="{ name: 'legalaspects-contracts-employees'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormContractEmployee from '@/components/LegalAspects/Contracts/Employees/FormContractEmployeeComponent.vue';
import Alerts from '@/utils/Alerts.js';

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
    }
  },
  created(){
    axios.get(`/legalAspects/employeeContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>