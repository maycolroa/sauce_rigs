<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="ADMINISTRAR INTERESES"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['interests_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-interest-create'}" variant="primary">Crear interes</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['interests_r']"
                configName="legalaspects-lm-interests"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'lm-interests',
  metaInfo: {
    title: 'Intereses'
  }
}
</script>
