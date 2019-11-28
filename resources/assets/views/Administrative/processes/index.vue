<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`ADMINISTRAR ${keywordCheck('processes')}`"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['processes_c']">
            <b-btn :to="{name:'administrative-processes-create'}" variant="primary">Crear {{ keywordCheck('process') }}</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-processes"
                v-if="auth.can['processes_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'processes',
  metaInfo() {
    return {
      title: `${this.keywordCheck('processes')}`
    }
  }
}
</script>
