<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="ADMINISTRAR TIPOS DE NORMA"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['risksAspects_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-type-create'}" variant="primary">Crear Tipo</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['risksAspects_r']"
                configName="legalaspects-lm-type"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'lm-type',
  metaInfo: {
    title: 'Tipos de Normas'
  }
}
</script>
