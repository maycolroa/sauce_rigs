<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="EDITAR RIESGOS"
      url="industrialsecure-risks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-risk-form
                :url="`/industrialSecurity/risk/${this.$route.params.id}`"
                method="PUT"
                :risk="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-risks'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureRiskForm from '@/components/IndustrialSecure/RiskMatrix/Risk/FormRiskComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-risks-edit',
  metaInfo: {
    title: 'Riesgos - Editar'
  },
  components:{
    IndustrialSecureRiskForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/risk/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>