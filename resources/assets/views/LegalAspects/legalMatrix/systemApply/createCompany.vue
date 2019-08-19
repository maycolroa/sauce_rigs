<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Sistemas que Aplican /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-system-apply-component
                url="/legalAspects/legalMatrix/systemApply"
                :custom="true"
                method="POST"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormSystemApplyComponent from '@/components/LegalAspects/LegalMatrix/SystemApply/FormSystemApplyComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-system-apply-company-create',
  metaInfo: {
    title: 'Sistemas que Aplican - Crear'
  },
  components:{
    FormSystemApplyComponent
  },
  data(){
    return {
    }
  }
}
</script>
