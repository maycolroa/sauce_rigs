<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR TIPO DE EVENTO"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['reinc_disease_origin_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'reinstatements-disease-origin-create'}" variant="primary">Crear Tipo de evento</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['reinc_disease_origin_r']"
                configName="reinstatements-disease-origin"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-disease-origin',
  metaInfo: {
    title: 'TIpo de evento'
  }
}
</script>
