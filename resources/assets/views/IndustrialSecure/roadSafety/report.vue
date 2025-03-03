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
                                ref=""/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
        <!--<b-card>
            <b-card-body>
                <div>
                    <center><h3>Tipo 2</h3></center>
                </div>
                 <div style="padding-top: ">
                    <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
                        <b-row>
                            <b-col>
                                <div><b># Inspecciones:</b> {{ informationType2.inspections }}</div>
                                <div><b># Cumplimientos:</b> {{informationType2.t_cumple}}</div>
                                <div><b># No Cumplimientos:</b> {{informationType2.t_no_cumple}}</div>
                                <div><b># Cumplimientos Parciales:</b> {{informationType2.t_cumple_p}}</div>
                            </b-col>
                            <b-col>
                                <div><b>% Cumplimientos:</b> {{informationType2.p_cumple}}</div>
                                <div><b>% No Cumplimientos:</b> {{informationType2.p_no_cumple}}</div>
                                <div><b>% Cumplimientos Parciales:</b> {{informationType2.p_parcial}}</div>
                            </b-col>
                            <b-col>
                                <div><b># Planes de acción realizados:</b> {{ informationType2.pa_realizados }}</div>
                                <div><b># Planes de acción no realizados:</b> {{ informationType2.pa_no_realizados }}</div>
                            </b-col>
                        </b-row>
                    </b-card>
                </div>
                <div>
                    <vue-advanced-select class="col-md-6" v-model="table" :multiple="false" :options="options" :hide-selected="false" @input="refreshData2" name="table" label="Tabla" placeholder="Seleccione una opción">
                    </vue-advanced-select>
                </div>
                <vue-table
                    ref="tableReport2"
                    v-if="auth.can['ph_inspections_r']"
                    configName="dangerousconditions-inspections-report-type-2"
                    :customColumnsName="true" 
                    @filtersUpdate="setFilters"
                    :params="{table: table, filters}"
                ></vue-table>
            </b-card-body>
        </b-card>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Inspecciones" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="inspectionsSelectedType2" :options="selectBar" :allowEmpty="false" :searchable="true" name="inspectionsSelectedType2">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Número de Inspecciones Planeadas</h4>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-12">
                            <chart-bar 
                                :chart-data="inspectionType2Data"
                                title="Número de Inspecciones Planeadas realizadas"
                                color-line="red"
                                ref=""/>
                        </div>
                    </b-row>
                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Porcentaje de Cumplimiento</h4>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-12">
                            <chart-bar-compliance 
                                :chart-data="complianceType2Data"
                                title="Porcentaje de Cumplimiento"
                                color-line="red"
                                ref=""/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>-->
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
                }/*,
                theme: {
                    labels: [],
                    datasets: []
                },
                headquarter: {
                    labels: [],
                    datasets: []
                },
                area: {
                    labels: [],
                    datasets: []
                }*/
            },
        }
    },
    created() {
        /*this.updateTotales()
        this.fetchSelect('selectBar', '/selects/multiselectBarInspection')*/
        this.fetch()
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
                    console.log(data)
                    this.update(data);
                    console.log(this.informs)
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