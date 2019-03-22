<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Áreas
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['areas_c']">
            <b-btn :to="{name:'administrative-areas-create'}" variant="primary">Crear Área</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-areas"
                v-if="auth.can['areas_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'areas',
  metaInfo: {
    title: 'Áreas'
  }
}
</script>
