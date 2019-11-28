<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR CONCLUSIONES LABORALES"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['reinc_labor_conclusion_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'reinstatements-labor-conclusions-create'}" variant="primary">Crear Conclusión laboral</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['reinc_labor_conclusion_r']"
                configName="reinstatements-labor-conclusions"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-labor-conclusions',
  metaInfo: {
    title: 'Conclusiones laborales'
  }
}
</script>
