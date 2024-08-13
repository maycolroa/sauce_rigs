<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="LIQUIDACIÓN"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']" style="height: 1200px">
            <form-employee-swith
              url="/legalAspects/employeeContract/liquidated"
              :employee="data"
              :cancel-url="{ name: 'legalaspects-contracts-employees'}"
            />
        </b-card-body>
        <b-card-body v-else>
            <form-employee-swith
              url="/legalAspects/employeeContract/liquidated"
              :employee="data"
              :cancel-url="{ name: 'legalaspects-contracts-employees'}"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEmployeeSwith from '@/components/LegalAspects/Contracts/Employees/FormLiquidadoComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contracts-employees-liquidated',
  metaInfo() {
    return {
      title: 'Liquidación'
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
