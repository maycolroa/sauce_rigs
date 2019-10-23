<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="ADMINISTRAR TEMAS AMBIENTALES"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['risksAspects_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-riskaspect-create'}" variant="primary">Crear Tema Ambiental</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['risksAspects_r']"
                configName="legalaspects-lm-riskaspect"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'lm-riskaspect',
  metaInfo: {
    title: 'Temas Ambientales'
  }
}
</script>
