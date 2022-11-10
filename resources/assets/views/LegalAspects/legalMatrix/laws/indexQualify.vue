<template>
  <div>
    <header-module
      title="MI MATRIZ LEGAL"
      subtitle="EVALUAR NORMAS"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                v-if="auth.can['laws_qualify'] || auth.can['laws_qualify_view']"
                configName="legalaspects-lm-laws-qualify"
                :params="{ qualify : true }"
                :customColumnsName="true"
                @rowClick="redirectQualify"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-laws-qualify',
  metaInfo: {
    title: 'Evaluar Normas'
  },
  methods: {
    redirectQualify(row) {
      let id = row.id;
      this.$router.push({name: 'legalaspects-lm-law-qualify-view', params : { id }});
    }
  }
}
</script>
