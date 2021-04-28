<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="EDITAR SUBPROCESO"
      url="industrialsecure-subprocesses"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-sub-process-form
                :url="`/industrialSecurity/subProcess/${this.$route.params.id}`"
                method="PUT"
                :subProcess="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-subprocess'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureSubProcessForm from '@/components/IndustrialSecure/RiskMatrix/SubProcess/FormSubProcessComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-subprocesses-edit',
  metaInfo: {
    title: 'Subprocesos - Editar'
  },
  components:{
    IndustrialSecureSubProcessForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/subProcess/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>