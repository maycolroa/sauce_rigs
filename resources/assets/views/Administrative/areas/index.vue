<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`ADMINISTRAR ${keywordCheck('areas')}`"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['areas_c']">
            <b-btn :to="{name:'administrative-areas-create'}" variant="primary">Crear {{ keywordCheck('area') }}</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-areas"
                v-if="auth.can['areas_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'areas',
  metaInfo() {
    return {
      title: `${this.keywordCheck('areas')}`
    }
  }
}
</script>
