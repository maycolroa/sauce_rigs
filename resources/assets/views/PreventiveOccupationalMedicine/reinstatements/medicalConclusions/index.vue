<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR CONCLUSIONES MÉDICAS"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['reinc_labor_conclusion_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'reinstatements-medical-conclusions-create'}" variant="primary">Crear Conclusión médica</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['reinc_medical_conclusion_r']"
                configName="reinstatements-medical-conclusions"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-medical-conclusions',
  metaInfo: {
    title: 'Conclusiones medicas'
  }
}
</script>
