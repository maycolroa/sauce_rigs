<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="CREAR TRASLADO"
      url="industrialsecure-epps-transactions-transfers-location"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                url="/industrialSecurity/epp/transfer"
                method="POST"
                employees-data-url="/selects/employees"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-transfers-location'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementTransfersLocation.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-transfers-location-create',
  metaInfo: {
    title: 'Traslado - Crear'
  },
  components:{
    TransactionForm
  },
  data(){
    return {
    }
  }
}
</script>
