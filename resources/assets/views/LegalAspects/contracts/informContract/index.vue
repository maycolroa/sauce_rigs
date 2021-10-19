<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR EVALUACIONES REALIZADAS"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']"> 
            <b-btn :to="{name:'legalaspects-evaluations'}" variant="secondary">Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']"
                configName="legalaspects-evaluations-lessee"
                ></vue-table>
              <vue-table
                v-else
                configName="legalaspects-evaluations-contracts"
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
  name: 'evaluations-contracts',
  metaInfo: {
    title: 'Evaluaciones Realizadas'
  }
}
</script>
