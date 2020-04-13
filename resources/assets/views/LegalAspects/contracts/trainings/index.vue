<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR CAPACITACIONES"
      url="legalaspects-contractor"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_training_c']">
            <b-btn :to="{name:'legalaspects-contracts-trainings-create'}" variant="primary">Crear Capacitación</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contracts-trainings"
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
    title: 'Capacitaciones'
  }
}
</script>
