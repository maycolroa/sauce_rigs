<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR CAPACITACIONES"
      url="legalaspects-contracts-trainings"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_training_c']">
            <b-btn :to="{name:'legalaspects-contracts-trainings-virtual-create'}" variant="primary">Crear Capacitación Virtual</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contracts-trainings-virtual"
                v-if="auth.can['contracts_training_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'trainings',
  metaInfo: {
    title: 'Capacitaciones Virtuales'
  }
}
</script>
