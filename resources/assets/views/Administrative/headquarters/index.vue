<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`ADMINISTRAR ${keywordCheck('headquarters')}`"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['headquarters_c']">
            <b-btn :to="{name:'administrative-headquarters-create'}" variant="primary">Crear {{ keywordCheck('headquarter') }}</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-headquarters"
                v-if="auth.can['headquarters_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'headquarters',
  metaInfo() {
    return {
      title: `${this.keywordCheck('headquarters')}`
    }
  }
}
</script>
