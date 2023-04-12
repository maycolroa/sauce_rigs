<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="ENVIO DE BOLETIN"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['newsletterSend_c']">
            <b-btn :to="{name:'system-newslettersend-create'}" variant="primary">Crear Boletin</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-newslettersend"
                v-if="auth.can['newsletterSend_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'newslettersend',
  metaInfo: {
    title: 'Envio de boletin'
  }
}
</script>
