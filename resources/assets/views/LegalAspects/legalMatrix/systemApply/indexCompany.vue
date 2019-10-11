<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="MIS SISTEMAS"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['systemApplyCustom_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-system-apply-company-create'}" variant="primary">Crear Sistema que Aplica</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['systemApplyCustom_r']"
                configName="legalaspects-lm-system-apply-company"
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
  name: 'legalaspects-lm-system-apply-company',
  metaInfo: {
    title: 'Mis Sistemas que Aplican'
  }
}
</script>
