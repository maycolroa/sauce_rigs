<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Entidades
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-entity-create'}" variant="primary">Crear Entidad</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-lm-entity"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'lm-entity',
  metaInfo: {
    title: 'Entidades'
  }
}
</script>
