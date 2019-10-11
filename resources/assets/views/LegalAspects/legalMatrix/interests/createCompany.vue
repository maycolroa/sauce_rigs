<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="CREAR INTERES"
      url="legalaspects-lm-interest-company"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-interest-component
                url="/legalAspects/legalMatrix/interest"
                :custom="true"
                method="POST"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInterestComponent from '@/components/LegalAspects/LegalMatrix/Interest/FormInterestComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-interest-company-create',
  metaInfo: {
    title: 'Intereses - Crear'
  },
  components:{
    FormInterestComponent
  },
  data(){
    return {
    }
  }
}
</script>
