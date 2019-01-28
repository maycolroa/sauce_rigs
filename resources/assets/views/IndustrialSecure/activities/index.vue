<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Actividades
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'industrialsecure-activities-create'}" variant="primary">Crear Actividad</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-activities"
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
