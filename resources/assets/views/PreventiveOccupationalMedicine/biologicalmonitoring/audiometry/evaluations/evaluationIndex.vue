<template>
  <div>
    <header-module
      title="AUDIOMETRIAS EVALUACIONES"
      subtitle="ADMINISTRAR EVALUACIONES REALIZADAS"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" > 
            <b-btn :to="{name:'audiometry-evaluations'}" variant="secondary">Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             
              <vue-table
                configName="biologicalmonitoring-evaluations-perform"
                :modelId="`${this.$route.params.id}`"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'audiomery-evaluations-perform',
  metaInfo: {
    title: 'Evaluaciones Realizadas'
  }
}
</script>
