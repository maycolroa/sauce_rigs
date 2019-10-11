<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGRO"
      subtitle="ADMINISTRAR PELIGROS"
      url="industrialsecure"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['dangers_c']"> 
            <b-btn :to="{name:'industrialsecure-dangers-create'}" variant="primary">Crear Peligro</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-dangers"
                v-if="auth.can['dangers_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangers',
  metaInfo: {
    title: 'Peligros'
  }
}
</script>
