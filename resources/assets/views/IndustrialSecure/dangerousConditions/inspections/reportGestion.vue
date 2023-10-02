<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="REPORTE DE INSPECCIONES PLANEADAS"
      url="dangerousconditions-inspection-report-menu"
    />

    <div class="col-md">
        <b-card border-variant="primary" title="Gestión de cumplimiento" class="mb-3 box-shadow-none">
            <!--<b-card-header class="with-elements">
                <div class="card-title-elements"> 
                    <b-btn v-if="auth.can['ph_inspections_report_export']" variant="primary" @click="exportReport()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
                </div>
            </b-card-header>-->
            <b-card-body>
                <div>
                    <p><b>{{message}}</b></p>
                </div>
                <vue-table
                    ref="tableReportGestion"
                    v-if="auth.can['ph_inspections_r']"
                    configName="dangerousconditions-inspections-report-gestion"
                    :customColumnsName="true"
                    @filtersUpdate="setFilters"
                ></vue-table>
            </b-card-body>
        </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import GlobalMethods from '@/utils/GlobalMethods.js';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import ChartBarCompliance from '@/components/ECharts/ChartBarCompliance.vue';

export default {
    name: 'dangerousconditions-inspections-report-gestion',
    metaInfo: {
        title: 'Inspecciones Planeadas - Reportes'
    },
    components:{
        VueAdvancedSelect,
        ChartBar,
        ChartBarCompliance
    },
    data () {
        return {
            isLoading: false,
            filters: [],
            message: 'Por Defecto se aplicara el filtro de fecha con el ultimo mes transcurrido'
        }
    },
    created() {},
    watch: {
        filters: {
            handler(val) {
                this.message = 'Cargando registros';
            },
            deep: true,
        }
    },
    computed: {},
    methods: {
        setFilters(value)
        { 
            this.filters = value
        }
    }
}
</script>