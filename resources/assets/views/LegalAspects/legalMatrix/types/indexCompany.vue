<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="MIS TIPOS"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['typesCustom_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-type-company-create'}" variant="primary">Crear Tipo</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['typesCustom_r']"
                configName="legalaspects-lm-type-company"
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
  name: 'legalaspects-lm-type-company',
  metaInfo: {
    title: 'Mis Entidades'
  }
}
</script>
