<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CAPACITACIONES VIRTUALES REALIZADAS"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-contracts-trainings-virtual'}" variant="secondary">Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
              <vue-table
                configName="legalaspects-contracts-trainings-employees"
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
  name: 'trainings-employees',
  metaInfo: {
    title: 'Capacitaciones Realizadas'
  }
}
</script>
