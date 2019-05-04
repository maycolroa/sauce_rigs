<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Lista de Contratistas
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-contractor-create'}" variant="primary">Crear contratista o arrendatario</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contractor"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contractor',
  metaInfo: {
    title: 'Lista de Contratistas'
  }
}
</script>
