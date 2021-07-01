<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="EDITAR MACROPROCESO"
      url="industrialsecure-riskmatrix-macroprocesses"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-macroprocesses
                :url="`/industrialSecurity/risksMatrix/macroprocess/${this.$route.params.id}`"
                method="PUT"
                :macroprocess="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-riskmatrix-macroprocesses'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormMacroprocesses from '@/components/IndustrialSecure/RiskMatrix/FormMacroprocessesComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-riskmatrix-macroprocesses-edit',
  metaInfo() {
    return {
      title: "Macroproceso - Editar"
    }
  },
  components:{
    FormMacroprocesses
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/risksMatrix/macroprocess/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>