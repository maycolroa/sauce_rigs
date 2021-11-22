<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="ADMINISTRAR UBICACIONES"
      url="industrialsecure-epp"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['location_c']"> 
            <b-btn :to="{name:'industrialsecure-epps-locations-create'}" variant="primary">Crear Ubicación</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-epps-location"
                :customColumnsName="true" 
                v-if="auth.can['location_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'epps-locations',
  metaInfo: {
    title: 'Ubicaciones'
  }
}
</script>
