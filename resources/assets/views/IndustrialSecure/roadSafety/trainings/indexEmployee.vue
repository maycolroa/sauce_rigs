<template>
  <div>
    <header-module
      title="CAPACITACIONES"
      subtitle="CAPACITACIONES REALIZADAS"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'industrialsecure-roadsafety-trainings'}" variant="secondary">Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
              <vue-table
                configName="industrialsecure-roadsafety-trainings-employees"
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
  name: 'roadSafety-trainings-employees',
  metaInfo: {
    title: 'Capacitaciones Realizadas'
  }
}
</script>
