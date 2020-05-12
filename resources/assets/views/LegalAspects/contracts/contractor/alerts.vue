<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CONFIGURAR ALERTAS"
      url="legalaspects-contractor"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-contract-alerts
                url="/legalAspects/contracts/saveAlerts"
                method="POST"
                :configuration="data"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormContractAlerts from '@/components/LegalAspects/Contracts/ContractLessee/FormContractAlertsComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'contracts-alerts',
  metaInfo: {
    title: 'Alertas'
  },
  components:{
    FormContractAlerts
  },
  data(){
    return {
      data: []
    }
  },
  /*created(){
    //axios para obtener las alertas
    axios.post("/legalAspects/contracts/getAlerts")
    .then(response => {
      this.documents = response.data;
      this.ready = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }*/
}
</script>
