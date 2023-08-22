<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR RESTRICCIONES"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['reinc_restrictions_c']">
            <b-btn :to="{name:'reinstatements-restrictions-create'}" variant="primary">Crear Restricción</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="reinstatements-restrictions"
                v-if="auth.can['reinc_restrictions_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'restrictions',
  metaInfo: {
    title: 'Restricciones'
  }
}
</script>
