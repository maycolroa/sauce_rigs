<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="TRANSACCIONES DE ELEMENTOS"
      url="industrialsecure-epps-elements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['transaction_c']"> 
            <b-btn :to="{name:'industrialsecure-epps-transactions-create'}" variant="primary">Crear Entrega</b-btn>&nbsp;&nbsp;
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-epps-transactions"
                v-if="auth.can['transaction_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'epps-transactions',
  metaInfo: {
    title: 'Transacciones'
  }
}
</script>

