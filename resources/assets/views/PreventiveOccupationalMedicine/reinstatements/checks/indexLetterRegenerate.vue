<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR CARTAS"
      url="reinstatements-checks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'reinstatements-checks'}" variant="default"> Regresar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
          <div>
            <vue-table
              configName="reinstatements-letter-recommendations"
              v-if="auth.can['reinc_checks_r']"
              ></vue-table>
          </div>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import FilterReincChecks from '@/components/Filters/FilterReincChecks.vue';

export default {
  name: 'reinstatements-checks-letter-regenerate',
  metaInfo: {
    title: 'Cartas'
  },
  components:{
      FilterReincChecks
  },
  data () {
    return {
      isLoading: false,
    }
  },
  watch: {},
  methods: {}
}
</script>
