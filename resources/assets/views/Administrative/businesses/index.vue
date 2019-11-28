<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`ADMINISTRAR ${keywordCheck('businesses')}`"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['businesses_c']">
            <b-btn :to="{name:'administrative-businesses-create'}" variant="primary">Crear {{ keywordCheck('businesses') }}</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-businesses"
                v-if="auth.can['businesses_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'businesses',
  metaInfo() {
    return {
      title: `${this.keywordCheck('businesses')}`
    }
  }
}
</script>
