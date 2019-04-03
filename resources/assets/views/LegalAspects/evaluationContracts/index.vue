<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Evaluaciones Realizadas
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-evaluations'}" variant="secondary">Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-evaluations-contracts"
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
  name: 'evaluations-contracts',
  metaInfo: {
    title: 'Evaluaciones Realizadas'
  }
}
</script>
