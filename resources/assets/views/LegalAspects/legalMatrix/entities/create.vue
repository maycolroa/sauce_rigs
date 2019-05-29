<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Entidades /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-entity-component
                url="/legalAspects/legalMatrix/entity"
                method="POST"
                :cancel-url="{ name: 'legalaspects-lm-entity'}"/>
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
