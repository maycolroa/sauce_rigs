<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="MIS INTERESES"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['interestsCustom_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-interest-company-create'}" variant="primary">Crear interes</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['interestsCustom_r']"
                configName="legalaspects-lm-interests-company"
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
  name: 'lm-interests-company',
  metaInfo: {
    title: 'Mis Intereses'
  }
}
</script>
