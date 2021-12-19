<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="VER ELEMENTO"
      url="industrialsecure-epps-transactions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                :delivery="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-epps-transactions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementAsignComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-view',
  metaInfo: {
    title: 'Transacciones - Ver'
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