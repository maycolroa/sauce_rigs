<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Sistemas que Aplican
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-system-apply-create'}" variant="primary">Crear Sistema que Aplica</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-lm-system-apply"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-system-apply',
  metaInfo: {
    title: 'Sistemas que Aplican'
  }
}
</script>
