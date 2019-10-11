<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="ADMINISTRAR SEDES"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['headquarters_c']">
            <b-btn :to="{name:'administrative-headquarters-create'}" variant="primary">Crear Sede</b-btn>
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
  metaInfo: {
    title: 'Sedes'
  }
}
</script>
