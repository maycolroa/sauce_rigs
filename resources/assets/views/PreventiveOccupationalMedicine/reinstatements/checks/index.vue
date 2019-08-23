<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Reportes
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['reinc_checks_c']">
            <b-btn :to="{name:'reinstatements-checks-create'}" variant="primary">Crear Reporte</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
          <div>
              <filter-reinc-checks 
                  v-model="filters" 
                  configName="reinstatements-checks" 
                  :isDisabled="isLoading"/>
          </div>
          <div>
            <vue-table
              ref="tableCheck"
              configName="reinstatements-checks"
              :customColumnsName="true"
              v-if="auth.can['reinc_checks_r']"
              :params="{filters: filters}"
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
  name: 'reinstatements-checks',
  metaInfo: {
    title: 'Reportes'
  },
  components:{
      FilterReincChecks
  },
  data () {
    return {
      filters: [],
      isLoading: false,
    }
  },
  watch: {
    filters: {
      handler(val){
        this.fetch()
      },
      deep: true
    }
  },
  methods: {
    fetch()
    {
      this.$refs.tableCheck.refresh()
    }
  }
}
</script>
