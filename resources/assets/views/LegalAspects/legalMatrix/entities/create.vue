<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="CREAR ENTIDAD"
      url="legalaspects-lm-entity"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-entity-component
                url="/legalAspects/legalMatrix/entity"
                method="POST"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEntityComponent from '@/components/LegalAspects/LegalMatrix/Entity/FormEntityComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-entity-create',
  metaInfo: {
    title: 'Entidades - Crear'
  },
  components:{
    FormEntityComponent
  },
  data(){
    return {
    }
  }
}
</script>
