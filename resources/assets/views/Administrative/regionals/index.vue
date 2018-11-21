<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Regionales
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'administrative-regionals-create'}" variant="primary">Crear Regional</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-regionals"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'regionals',
  metaInfo: {
    title: 'Regionales'
  }
}
</script>
