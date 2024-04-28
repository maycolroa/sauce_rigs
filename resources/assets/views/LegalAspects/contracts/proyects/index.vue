<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR PROYECTO"
      url="legalaspects-contracts"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_proyects_c']">
            <b-btn :to="{name:'legalaspects-contracts-proyects-create'}" variant="primary">Crear Proyecto</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contracts-proyects"
                v-if="auth.can['contracts_proyects_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'proyects',
  metaInfo: {
    title: 'Proyectos'
  }
}
</script>
