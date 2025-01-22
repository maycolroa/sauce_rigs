<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="CREAR TIPO"
      url="legalaspects-lm-type"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-type-component
                url="/legalAspects/legalMatrix/type"
                :cancel-url="{ name: 'legalaspects-lm-type'}"
                method="POST"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTypeComponent from '@/components/LegalAspects/LegalMatrix/Type/FormTypeComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-type-create',
  metaInfo: {
    title: 'Tipos - Crear'
  },
  components:{
    FormTypeComponent
  },
  data(){
    return {
    }
  }
}
</script>
