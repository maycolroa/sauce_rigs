<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="ADMINISTRAR CONDUCTORES"
      url="industrialsecure-roadsafety"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['roadsafety_drivers_c']">
            <b-btn :to="{name:'industrialsecure-roadsafety-drivers-create'}" variant="primary">Crear Conductor</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-roadsafety-drivers"
                :customColumnsName="true" 
                v-if="auth.can['roadsafety_drivers_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'drivers',
  metaInfo: {
    title: 'Conductores'
  }
}
</script>
