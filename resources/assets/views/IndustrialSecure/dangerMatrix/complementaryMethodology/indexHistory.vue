<template>
  <div>
    <header-module
			title="METODOLOGIAS COMPLEMENTARIAS DE VALORACION DE RIESGOS"
			subtitle="METODOLOGIAS HISTORICO"
      url="industrialsecure-complementary-methodology"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <vue-table
            configName="industrialsecure-complementary-methodology-history"
            :params="{metodologyId: `${this.$route.params.id}`}"
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
  name: 'industrialsecure-complementary-methodology',
  metaInfo: {
    title: 'Metodologias - Historico'
  },
  data () {
    return {}
  }
}
</script>
