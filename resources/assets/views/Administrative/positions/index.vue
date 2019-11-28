<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`ADMINISTRAR ${keywordCheck('positions')}`"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['positions_c']">
            <b-btn :to="{name:'administrative-positions-create'}" variant="primary">Crear {{ keywordCheck('position') }}</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-positions"
                v-if="auth.can['positions_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'positions',
  metaInfo() {
    return {
      title: `${this.keywordCheck('positions')}`
    }
  }
}
</script>
