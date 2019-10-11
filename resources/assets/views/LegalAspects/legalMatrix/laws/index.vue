<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="ADMINISTRAR NORMAS"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['laws_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-law-create'}" variant="primary">Crear Norma</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['laws_r']"
                configName="legalaspects-lm-laws"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>
<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-laws',
  metaInfo: {
    title: 'Normas'
  }
}
</script>
