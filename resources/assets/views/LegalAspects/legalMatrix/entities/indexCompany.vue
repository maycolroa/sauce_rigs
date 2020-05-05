<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="MIS ENTIDADES"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['entitiesCustom_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-entity-company-create'}" variant="primary">Crear Entidad</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['entitiesCustom_r']"
                configName="legalaspects-lm-entity-company"
                :params="{ custom : true }"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-entity-company',
  metaInfo: {
    title: 'Mis Entidades'
  }
}
</script>
