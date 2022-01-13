<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="VER DEVOLUCIÓN"
      url="industrialsecure-epps-transactions-returns"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                :returns="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-returns'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementReturnsComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-returns-view',
  metaInfo: {
    title: 'Transacciones Devoluciones - Ver'
  },
  components:{
    TransactionForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/epp/transaction/${this.$route.params.id}`)
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