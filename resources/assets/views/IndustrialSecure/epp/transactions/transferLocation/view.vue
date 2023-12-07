<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="VER TRASLADO"
      url="industrialsecure-epps-transactions-transfers-location"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                :transfer="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-transfers-location'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementTransfersLocation.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-transfers-location-view',
  metaInfo: {
    title: 'Traslado - Ver'
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
    axios.get(`/industrialSecurity/epp/transfer/${this.$route.params.id}`)
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