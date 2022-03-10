<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="REPORTES"
      url="industrialsecure-epp"
    />

    <div class="col-md">
      <b-card no-body>
        <div>
          <filter-general 
            v-model="filters" 
            configName="industrialsecure-epp-report" />
        </div>

        <b-tabs card pills class="nav-responsive-md md-pills-light">
          <b-tab>
            <template slot="title">
                <strong>Reporte Saldos</strong> 
            </template>
            <b-card>
              <b-card-body>
                <vue-table
                  configName="industrialsecure-epp-reports"
                  v-if="auth.can['elements_r']"
                  :params="{filters}"
                  ref="saldosElements"
                ></vue-table>
              </b-card-body>
            </b-card>
          </b-tab>
          <b-tab>
            <template slot="title">
                <strong>Reporte Elementos Asignados</strong> 
            </template>
            <b-card>
              <b-card-body>
                <vue-table
                  configName="industrialsecure-epp-reports-employees"
                  v-if="auth.can['elements_r']"
                  :params="{filters}"
                  ref="elementsAsigned"
                ></vue-table>
              </b-card-body>
            </b-card>
          </b-tab>
        </b-tabs>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
  name: 'industrialsecure-epp-reports',
  metaInfo: {
    title: 'EPP - Reportes'
  },
  components:{
      FilterGeneral
  },
  data () {
        return {
            filters: [],
        }
    },
    watch: {
        filters: {
            handler(val){
                this.$refs.saldosElements.refresh()
                this.$refs.elementsAsigned.refresh()
            },
            deep: true
        }
    }
}
</script>
