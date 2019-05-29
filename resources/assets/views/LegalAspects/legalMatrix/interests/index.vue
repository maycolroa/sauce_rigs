<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Intereses
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-interest-create'}" variant="primary">Crear interes</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-lm-interests"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'lm-interests',
  metaInfo: {
    title: 'Intereses'
  }
}
</script>
