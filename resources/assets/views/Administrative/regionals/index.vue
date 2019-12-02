<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`ADMINISTRAR ${keywordCheck('regionals', 'Regionales')}`"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['regionals_c']">
            <b-btn :to="{name:'administrative-regionals-create'}" variant="primary">Crear {{ keywordCheck('regional') }}</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-regionals"
                v-if="auth.can['regionals_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'regionals',
  metaInfo() {
    return {
      title: `${this.keywordCheck('regionals', 'Regionales')}`
    }
  }
}
</script>
