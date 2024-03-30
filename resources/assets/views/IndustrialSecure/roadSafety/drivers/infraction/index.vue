<template>
  <div>
		<header-module
      title="SEGURIDAD VIAL"
			subtitle="ADMINISTRAR CONDUCTORES - INFRACCIONES"
			url="industrialsecure-roadsafety-drivers"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'industrialsecure-roadsafety-drivers'}" variant="secondary">Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-roadsafety-infraction"
                :modelId="`${this.$route.params.id}`"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-roadsafety-drivers-infraction',
  metaInfo: {
    title: 'Conductores Infracciones'
  }
}
</script>
