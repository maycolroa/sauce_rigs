<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="VER SALIDA"
      url="industrialsecure-epps-transactions-exit"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                :exit="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-exit'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementExitComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-exit-view',
  metaInfo: {
    title: 'Salidas - Ver'
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
    axios.get(`/industrialSecurity/epp/exit/${this.$route.params.id}`)
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