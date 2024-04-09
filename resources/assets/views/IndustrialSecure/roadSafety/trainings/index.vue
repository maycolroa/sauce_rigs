<template>
  <div>
    <header-module
      title="CAPACITACIONES"
      subtitle="ADMINISTRAR CAPACITACIONES"
      url="industrialsecure-roadsafety"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['roadsafety_trainings_c']">
            <b-btn :to="{name:'industrialsecure-roadsafety-trainings-create'}" variant="primary">Crear Capacitación</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-roadsafety-trainings"
                v-if="auth.can['roadsafety_trainings_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'roadsafety-trainings',
  metaInfo: {
    title: 'Capacitaciones'
  }
}
</script>
