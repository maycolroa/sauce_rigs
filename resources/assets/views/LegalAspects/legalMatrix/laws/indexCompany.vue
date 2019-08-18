<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Mis Normas
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements" v-if="auth.can['lawsCustom_c']">
          <div class="card-title-elements"> 
            <b-btn :to="{name:'legalaspects-lm-law-company-create'}" variant="primary">Crear Norma</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['lawsCustom_r']"
                configName="legalaspects-lm-laws-company"
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
  name: 'legalaspects-lm-laws-company',
  metaInfo: {
    title: 'Mis Normas'
  }
}
</script>
