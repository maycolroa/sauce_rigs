<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR INFORMES MENSUALES REALIZADOS"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
            <b-btn :to="{name:'legalaspects-informs'}" variant="secondary">Regresar</b-btn>&nbsp;&nbsp;
            <b-btn v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']" :to="{name:'legalaspects-informs-report', id: `${this.$route.params.id}`}"
             variant="primary">Reporte</b-btn>
        </b-card-header>
        <b-card-body>
          <vue-table
            v-if="auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']"
            configName="legalaspects-informs-contracts-lesse"
            :customColumnsName="true" 
            :modelId="`${this.$route.params.id}`"
          ></vue-table>
          <vue-table
            v-else
            configName="legalaspects-informs-contracts"
            :customColumnsName="true" 
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
  name: 'inform-contracts',
  metaInfo: {
    title: 'Informes Mesuales Realizados'
  }
}
</script>
