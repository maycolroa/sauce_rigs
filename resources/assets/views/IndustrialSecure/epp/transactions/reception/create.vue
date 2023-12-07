<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="CREAR RECEPCIÓN"
      url="industrialsecure-epps-transactions-reception"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                url="/industrialSecurity/epp/reception"
                method="POST"
                :reception="data" 
                employees-data-url="/selects/employees"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-reception'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementReceptionLocation.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-reception-create',
  metaInfo: {
    title: 'Recepción - Crear'
  },
  components:{
    TransactionForm
  },
  data(){
    return {
    }
  },
  created(){
    axios.get(`/industrialSecurity/epp/reception/${this.$route.params.id}`)
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
