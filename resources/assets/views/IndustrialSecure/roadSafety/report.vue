<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="REPORTE DE SEGURIDAD VIAL"
      url="industrialsecure-roadsafety"
    />

    <div class="col-md">
        <b-card border-variant="primary" title="Documentos Conductores" class="mb-3 box-shadow-none">
            <b-card-body>
                <vue-table
                    ref="tableReportDocumentDrivers"
                    configName="industrialsecure-roadsafety-report-document-drivers"
                    @filtersUpdate="setFilters"
                ></vue-table>
            </b-card-body>
        </b-card>
        <b-card border-variant="primary" title="Documentos Vehículos" class="mb-3 box-shadow-none">
            <b-card-body>
                <vue-table
                    ref="tableReportDocumentVehicles"
                    configName="industrialsecure-roadsafety-report-document-vehicles"
                    :customColumnsName="true" 
                    @filtersUpdate="setFilters"
                ></vue-table>
            </b-card-body>
        </b-card>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Reportes" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Conductores con infracciones</h4>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-12">
                            <chart-bar 
                                :chart-data="informs.driverInfractions"
                                title="Número de infracciones por conductos"
                                color-line="red"
                                ref="driverInfractions"/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Reportes mantenimiento de vehículos por placa" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="informs.reportMaintenancePlate"
                        title="Reportes mantenimiento de vehículos por placa"
                        ref="reportMaintenancePlate"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Reportes mantenimiento de vehículos por año" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="informs.reportMaintenanceYear"
                        title="Reportes mantenimiento de vehículos por año"
                        ref="reportMaintenanceYear"/>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Reportes mantenimiento de vehículos por mes" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="informs.reportMaintenanceMonth"
                        title="Reportes mantenimiento de vehículos por mes"
                        ref="reportMaintenanceMonth"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Reportes mantenimiento de vehículos por tipo" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="informs.reportMaintenanceType"
                        title="Reportes mantenimiento de vehículos por tipo"
                        ref="reportMaintenanceType"/>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Reporte Combustible" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="reportSelected" :options="informs.selectBar" :allowEmpty="false" :searchable="true" name="reportSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Número de Galones</h4>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-12">
                            <chart-bar 
                                :chart-data="combustibleData"
                                title="Número de Galones"
                                color-line="red"
                                ref=""/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import GlobalMethods from '@/utils/GlobalMethods.js';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import ChartBarCompliance from '@/components/ECharts/ChartBarCompliance.vue';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
    name: 'roadSafety-report',
    metaInfo: {
        title: 'Seguridad Vial - Reportes'
    },
    components:{
        VueAdvancedSelect,
        ChartBar,
        ChartBarCompliance,
        FilterGeneral
    },
    data () {
        return {
            isLoading: false,
            filters: [],  
            informs: {
                driverInfractions: {
                    labels: [],
                    datasets: []
                },
                reportMaintenancePlate: {
                    labels: [],
                    datasets: []
                },
                reportMaintenanceYear: {
                    labels: [],
                    datasets: []
                },
                reportMaintenanceMonth: {
                    labels: [],
                    datasets: []
                },
                reportMaintenanceType: {
                    labels: [],
                    datasets: []
                },
                reporCombustiblePlate: {
                    labels: [],
                    datasets: []
                },
                reportCombustibleMonth: {
                    labels: [],
                    datasets: []
                },
                reportCombustibleYear: {
                    labels: [],
                    datasets: []
                },
                reportCombustibleCost: {
                    labels: [],
                    datasets: []
                },
                selectBar: [],
            },            
            reportSelected: 'reporCombustiblePlate',
        }
    },
    created() {
        /*this.updateTotales()
        this.fetchSelect('selectBar', '/selects/multiselectBarInspection')*/
        this.fetch()
    },
    computed: {
        combustibleData: function() {
            return this.informs[this.reportSelected]
        },
    },
    watch: {
        /*filters: {
            handler(val) {
                this.$refs.tableReport2.refresh()
            },
            deep: true,
        },*/
    },
    methods: {
        refreshData()
        {
            if (!this.isLoading)
                this.$refs.tableReport.refresh()
            //this.updateTotales()
        },
        refreshData2()
        {
            if (!this.isLoading)
                this.$refs.tableReport2.refresh()
            //this.updateTotales()
        },
        setFilters(value)
        { 
            this.filters = value
            this.updateTotales()
            this.fetch()
        },
        exportReport() {
            let postData = Object.assign({}, {table: this.table}, this.filters);
            axios.post('/industrialSecurity/dangerousConditions/inspection/exportReport', postData)
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        fetchSelect(key, url)
        {
            GlobalMethods.getDataMultiselect(url)
            .then(response => {
                this[key] = response;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
        },
        fetch()
        {
            if (!this.isLoading)
            {
                //console.log('buscando...')
                this.isLoading = true;

                let postData = Object.assign({}, this.filters);

                axios.post('/industrialSecurity/roadsafety/reports', postData)
                .then(data => {
                    this.update(data);
                    this.isLoading = false;
                })
                .catch(error => {
                    console.log(error);
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });
            }
        },
        update(data) {
            _.forIn(data.data, (value, key) => {
                if (this.informs[key]) {
                    this.informs[key] = value;
                }
            });
        },
        updateTotales()
        {
            let postData = Object.assign({}, {table: this.table}, this.filters);
            axios.post('/industrialSecurity/dangerousConditions/inspection/report/getTotals', postData)
                .then(response => {
                    this.information = response.data.data;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
            axios.post('/industrialSecurity/dangerousConditions/inspection/report/getTotalsType2', postData)
                .then(response => {
                    this.informationType2 = response.data.data;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        }
    }
}
</script>