<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="CREAR TEMA SST"
      url="legalaspects-lm-sstrisk"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-sst-risk-component
                url="/legalAspects/legalMatrix/sstRisk"
                method="POST"
                :cancel-url="{ name: 'legalaspects-lm-sstrisk'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormSstRiskComponent from '@/components/LegalAspects/LegalMatrix/SstRisk/FormSstRiskComponent.vue'; 
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-sstrisk-create',
  metaInfo: {
    title: 'Temas SST - Crear'
  },
  components:{
    FormSstRiskComponent
  },
  data(){
    return {
    }
  }
}
</script>
