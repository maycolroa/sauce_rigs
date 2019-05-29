<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Riesgos SST /</span> Crear
    </h4>


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
    title: 'Riesgos SST - Crear'
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
