<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="EDITAR TEMA AMBIENTAL"
      url="legalaspects-lm-riskaspect"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-risk-aspect-component
                :url="`/legalAspects/legalMatrix/riskAspect/${this.$route.params.id}`"
                method="PUT"
                :risk_aspect="data"
                :is-edit="true"
                :cancel-url="{ name: 'legalaspects-lm-riskaspect'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormRiskAspectComponent from '@/components/LegalAspects/LegalMatrix/RiskAspect/FormRiskAspectComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-riskaspect-edit',
  metaInfo: {
    title: 'Temas Ambientales - Editar'
  },
  components:{
    FormRiskAspectComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/legalMatrix/riskAspect/${this.$route.params.id}`)
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