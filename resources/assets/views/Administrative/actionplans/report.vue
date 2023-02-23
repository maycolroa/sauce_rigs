<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="REPORTE DE PLANES DE ACCIÓN"
      url="administrative-actionplans"
    />

    <div class="col-md">
        <b-card no-body>
            <b-card-body v-show="!isLoading">
                <vue-table
                    configName="action-plan-report"
                    @filtersUpdate="setFilters"
                ></vue-table>
            </b-card-body>
        </b-card>
        <b-card border-variant="primary" title="">
          <b-row>
            <b-col>
              <chart-pie 
                :chart-data="report_pie"
                title=""
                color-line="red"
                ref="report_pie"/>
            </b-col>
          </b-row>
        </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import ChartPie from '@/components/ECharts/ChartPie.vue';

export default {
    name: 'action-plan-report',
    metaInfo: {
        title: 'Planes de acción - Reportes'
    },
    components:{
      ChartPie
    },
    data () {
    return {
      filters: [],
      report_pie: {
          labels: [],
          datasets: []
      },
      isLoading: false,
    }
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
      this.fetch();
    },
    fetch()
    {
      if (!this.isLoading)
      {
        this.isLoading = true;
        axios.post('/administration/actionplan/reportPie', {filters: this.filters})
            .then(data => {
                this.report_pie = data.data;
                this.isLoading = false;
            })
            .catch(error => {
                console.log(error);
                this.isLoading = false;
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
      }
    }
  },
  created()
  {
    this.fetch();
  }
}
</script>