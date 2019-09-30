<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Inspecciones
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['ph_inspections_c']">
            <b-btn :to="{name:'dangerousconditions-inspections-create'}" variant="primary">Crear Inspección</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="dangerousconditions-inspections"
                v-if="auth.can['ph_inspections_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousConditions-inspections',
  metaInfo: {
    title: 'Inspecciones'
  }
}
</script>
