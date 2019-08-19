<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Intereses /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-interest-component
                url="/legalAspects/legalMatrix/interest"
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
  name: 'legalaspects-lm-interest-create',
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
