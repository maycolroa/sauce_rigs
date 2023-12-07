<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="CREAR SALIDA"
      url="industrialsecure-epps-transactions-exit"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                url="/industrialSecurity/epp/exit"
                method="POST"
                employees-data-url="/selects/employees"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-exit'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementExitComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-exit-create',
  metaInfo: {
    title: 'Salidas - Crear'
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
