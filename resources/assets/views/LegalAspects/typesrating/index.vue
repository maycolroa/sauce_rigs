<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Tipos de calificaciones
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-evaluations'}" variant="secondary">Regresar</b-btn>
            <b-btn :to="{name:'legalaspects-typesrating-create'}" variant="primary">Crear tipo de calificación</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-typesrating"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'types-rating',
  metaInfo: {
    title: 'Tipos de calificaciones'
  }
}
</script>
