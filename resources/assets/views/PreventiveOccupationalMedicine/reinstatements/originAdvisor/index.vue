<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR PROCEDENCIA DE RECOMENDACIONES"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['reinc_origin_advisor_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'reinstatements-origin-advisor-create'}" variant="primary">Crear Procedencia de recomendaciones</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['reinc_origin_advisor_r']"
                configName="reinstatements-origin-advisor"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-origin-advisor',
  metaInfo: {
    title: 'Procedencia de recomendaciones'
  }
}
</script>
