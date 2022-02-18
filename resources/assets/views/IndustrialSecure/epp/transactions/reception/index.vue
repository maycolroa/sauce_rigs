<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="RECEPCIONES"
      url="industrialsecure-epps-transactions-menu"
    />

    <div class="col-md">
      <b-card no-body>
        <!--<b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['transaction_c']"> 
            <b-btn :to="{name:'industrialsecure-epps-transactions-reception-create'}" variant="primary">Crear Recepción</b-btn>&nbsp;&nbsp;
          </div>
        </b-card-header>-->
        <b-card-body>
             <vue-table
                configName="industrialsecure-epps-transactions-reception"
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
  name: 'epps-transactions-reception',
  metaInfo: {
    title: 'Recepciones'
  }
}
</script>

