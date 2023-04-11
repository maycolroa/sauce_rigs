<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="ADMINISTRAR CASOS"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['reinc_checks_c']" :to="{name:'reinstatements-checks-create'}" variant="primary">Crear Caso</b-btn>
            <b-btn v-if="auth.can['reinc_checks_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
            <b-btn v-if="!auth.hasRole['Rol Visor Reincorporaciones']" :to="{name:'reinstatements-checks-letter-regenerate'}" variant="primary">Consulta de Cartas</b-btn>
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
    title: 'Casos'
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
    },
    exportData(){
      axios.post('/biologicalmonitoring/reinstatements/check/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
  }
}
</script>
