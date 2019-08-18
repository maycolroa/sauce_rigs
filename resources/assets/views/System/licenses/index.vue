<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Licencias
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['licenses_c']">
            <b-btn :to="{name:'system-licenses-create'}" variant="primary">Crear Licencia</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-licenses"
                v-if="auth.can['licenses_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'licenses',
  metaInfo: {
    title: 'Licencias'
  }
}
</script>
