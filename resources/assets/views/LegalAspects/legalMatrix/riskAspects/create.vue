<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="CREAR TEMA AMBIENTAL"
      url="legalaspects-lm-riskaspect"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-risk-aspect-component
                url="/legalAspects/legalMatrix/riskAspect"
                method="POST"
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
  name: 'legalaspects-lm-riskaspect-create',
  metaInfo: {
    title: 'Riesgos/Aspectos Ambientales - Crear'
  },
  components:{
    FormRiskAspectComponent
  },
  data(){
    return {
    }
  }
}
</script>
