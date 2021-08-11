<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="ADMINISTRAR SUBPROCESOS"
      url="industrialsecure-riskmatrix-menu"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['subProcesses_c']">
            <b-btn :to="{name:'industrialsecure-subprocesses-create'}" variant="primary">Crear Subprocesos</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-subprocess"
                v-if="auth.can['subProcesses_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'subprocesses',
  metaInfo: {
    title: 'Subprocesos'
  }
}
</script>
