<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Nivel localización en formulario
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'configurations-locationlevelform-create'}" variant="primary">Crear Nivel</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="configurations-locationlevelform"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'locationlevelform',
  metaInfo: {
    title: 'Nivel localización en formulario'
  }
}
</script>
