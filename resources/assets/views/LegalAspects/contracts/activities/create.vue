<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR ACTIVIDAD"
      url="legalaspects-contracts-activities"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <contract-activity-form
                url="/legalAspects/activityContract"
                method="POST"
                :cancel-url="{ name: 'legalaspects-contracts-activities'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import ContractActivityForm from '@/components/LegalAspects/Contracts/Activities/FormContractActivityComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-activities-create',
  metaInfo: {
    title: 'Actividades - Crear'
  },
  components:{
    ContractActivityForm
  },
  props: {
  },
  data(){
    return {
    }
  }
}
</script>
