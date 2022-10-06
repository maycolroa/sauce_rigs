<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="REPORTE DE PLANES DE ACCIÓN"
      url="administrative-actionplans"
    />

    <div class="col-md">
        <b-card no-body>
            <b-card-body>
                <vue-table
                    configName="action-plan-report"
                    @filtersUpdate="setFilters"
                ></vue-table>
            </b-card-body>
        </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'action-plan-report',
    metaInfo: {
        title: 'Planes de acción - Reportes'
    },
    components:{
    },
    data () {
    return {
      filters: []
    }
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    }
  }
}
</script>