<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="CREAR DEVOLUCIÓN"
      url="industrialsecure-epps-transactions-returns"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                url="/industrialSecurity/epp/transaction"
                method="POST"
                employees-data-url="/selects/employees"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-returns'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementReturnsComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-returns-create',
  metaInfo: {
    title: 'Transacciones Devoluciones- Crear'
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
