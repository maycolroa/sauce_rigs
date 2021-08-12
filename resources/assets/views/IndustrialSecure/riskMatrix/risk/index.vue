<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="ADMINISTRAR RIESGOS"
      url="industrialsecure-riskmatrix-menu"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['risks_c']">
            <b-btn :to="{name:'industrialsecure-risks-create'}" variant="primary">Crear Riesgos</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-risk"
                v-if="auth.can['risks_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'risks',
  metaInfo: {
    title: 'Riesgos'
  }
}
</script>
