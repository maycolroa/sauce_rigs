<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="ADMINISTRAR ITEMS"
      url="industrialsecure-accidentswork-index-causes"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['accidentsWork_c']">
            <b-btn :to="{name:'industrialsecure-accidentswork-causes-items-create'}" variant="primary">Crear Item</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-accidents-causes-items"
                v-if="auth.can['accidentsWork_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'accidentsWork-causes-items',
  metaInfo: {
    title: 'Causas - items'
  },
  data () {
    return {
    }
  }
}
</script>
