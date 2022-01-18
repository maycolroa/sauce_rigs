<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="CREAR INGRESO"
      url="industrialsecure-epps-transactions-income"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <transaction-form
                url="/industrialSecurity/epp/income"
                method="POST"
                employees-data-url="/selects/employees"
                :cancel-url="{ name: 'industrialsecure-epps-transactions-income'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import TransactionForm from '@/components/IndustrialSecure/Epp/FormElementIncomeComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-transactions-income-create',
  metaInfo: {
    title: 'Ingresos - Crear'
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
