<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Matriz de Peligros
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['dangerMatrix_c']" :to="{name:'industrialsecure-dangermatrix-create'}" variant="primary">Crear Matriz de Peligros</b-btn>
            <b-btn v-if="auth.can['dangerMatrix_view_report']" :to="{name:'industrialsecure-dangermatrix-report'}" variant="primary">Reporte</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-dangermatrix"
                :customColumnsName="true" 
                v-if="auth.can['dangerMatrix_r']" 
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangermatrix',
  metaInfo: {
    title: 'Matriz de Peligros'
  }
}
</script>
