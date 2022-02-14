<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="TRASLADOS"
      url="industrialsecure-epps-transactions-menu"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['transaction_c']"> 
            <b-btn :to="{name:'industrialsecure-epps-transactions-transfers-location-create'}" variant="primary">Crear Traslado</b-btn>&nbsp;&nbsp;
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-epps-transactions-transfers-location"
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
  name: 'epps-transactions-transfers-location',
  metaInfo: {
    title: 'Traslado entre ubicaciones'
  }
}
</script>

