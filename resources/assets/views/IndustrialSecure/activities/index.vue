<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="ADMINISTRAR ACTIVIDADES"
      url="industrialsecure"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['activities_c']">
            <b-btn :to="{name:'industrialsecure-activities-create'}" variant="primary">Crear Actividad</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-activities"
                v-if="auth.can['activities_r']"
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
