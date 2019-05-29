<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Riesgos SST
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-sstrisk-create'}" variant="primary">Crear Riesgo SST</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-lm-sstrisk"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'lm-sstrisk',
  metaInfo: {
    title: 'Riesgos SST'
  }
}
</script>
