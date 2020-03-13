<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR ACTIVIDADES"
      url="legalaspects-contracts"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_activities_c']">
            <b-btn :to="{name:'legalaspects-contracts-activities-create'}" variant="primary">Crear Actividad</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contracts-activities"
                v-if="auth.can['contracts_activities_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'activities',
  metaInfo: {
    title: 'Actividades'
  }
}
</script>
