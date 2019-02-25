<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Procesos
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['processes_c']">
            <b-btn :to="{name:'administrative-processes-create'}" variant="primary">Crear Proceso</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-processes"
                v-if="auth.can['processes_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'processes',
  metaInfo: {
    title: 'Procesos'
  }
}
</script>
