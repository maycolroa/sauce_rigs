<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Inspecciones /</span> Calificadas
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'dangerousconditions-inspections'}" variant="secondary">Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
              <vue-table
                configName="dangerousconditions-inspections-qualification"
                v-if="auth.can['ph_inspections_r']"
                :params="{inspectionId: `${this.$route.params.id}`}"
                ></vue-table>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousconditions-inspections-qualification',
  metaInfo: {
    title: 'Inspecciones - Calificadas'
  }
}
</script>
