<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="ADMINISTRAR VEHICULO"
      url="industrialsecure-roadsafety"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['roadsafety_vehicles_c']">
            <b-btn :to="{name:'industrialsecure-roadsafety-vehicles-create'}" variant="primary">Crear Vehículo</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-roadsafety-vehicles"
                :customColumnsName="true" 
                v-if="auth.can['roadsafety_vehicles_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'vehicles',
  metaInfo: {
    title: 'Vehiculos'
  }
}
</script>
