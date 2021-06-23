<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="REPORTE DE INSPECCIONES PLANEADAS"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
        <b-card no-body>
            <b-card-header class="with-elements">
                <div class="card-title-elements"> 
                    <b-btn v-if="auth.can['ph_inspections_report_export']" variant="primary" @click="exportReport()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
                </div>
            </b-card-header>
            <b-card-body>
                 <div style="padding-top: ">
                     <center><h3>Tipo 1</h3></center>
                    <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
                        <b-row>
                            <b-col>
                                <div><b># Inspecciones:</b> {{ information.inspections }}</div>
                                <div><b># Cumplimientos:</b> {{information.t_cumple}}</div>
                                <div><b># No Cumplimientos:</b> {{information.t_no_cumple}}</div>
                                <div><b># Cumplimientos Parciales:</b> {{information.t_cumple_p}}</div>
                            </b-col>
                            <b-col>
                                <div><b>% Cumplimientos:</b> {{information.p_cumple}}</div>
                                <div><b>% No Cumplimientos:</b> {{information.p_no_cumple}}</div>
                                <div><b>% Cumplimientos Parciales:</b> {{information.p_parcial}}</div>
                            </b-col>
                            <b-col>
                                <div><b># Planes de acción realizados:</b> {{ information.pa_realizados }}</div>
                                <div><b># Planes de acción no realizados:</b> {{ information.pa_no_realizados }}</div>
                            </b-col>
                        </b-row>
                    </b-card>
                </div>
                <div>
                    <vue-advanced-select class="col-md-6" v-model="table" :multiple="false" :options="options" :hide-selected="false" @input="refreshData" name="table" label="Tabla" placeholder="Seleccione una opción">
                    </vue-advanced-select>
                </div>
                <vue-table
                    ref="tableReport"
                    v-if="auth.can['ph_inspections_r']"
                    configName="dangerousconditions-inspections-report"
                    :customColumnsName="true" 
                    @filtersUpdate="setFilters"
                    :params="{table: table}"
                ></vue-table>
            </b-card-body>
        </b-card>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Inspecciones Planeadas" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="inspectionsSelected" :options="selectBar" :allowEmpty="false" :searchable="true" name="inspectionsSelected">
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
                                :chart-data="inspectionData"
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
                                :chart-data="complianceData"
                                title="Porcentaje de Cumplimiento"
                                color-line="red"
                                ref=""/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
        <b-card>
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
                    :params="{table: table}"
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

export default {
    name: 'dangerousconditions-inspections-report',
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
            information: {
                inspections: 0,
                t_cumple: 0,
                t_no_cumple: 0,
                t_cumple_p: 0,
                p_cumple: '0%',
                p_no_cumple: '0%',
                p_parcial: '0%',
                pa_realizados: 0,
                pa_no_realizados: 0
            },
            table: 'with_theme',
            options: [
					{ name:'Con Tema', value:'with_theme'},
					{ name:'Sin Tema', value:'without_theme'}
			],            
            selectBar: [],
            inspectionsSelected: 'inspection',
            inspectionsSelectedType2: 'inspection',
            inspections: {
                inspection: {
                    labels: [],
                    datasets: []
                },
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
                }
            },
            compliance: {
                inspection: {
                    labels: [],
                    datasets: []
                },
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
                }
            },
            informationType2: {
                inspections: 0,
                t_cumple: 0,
                t_no_cumple: 0,
                t_cumple_p: 0,
                p_cumple: '0%',
                p_no_cumple: '0%',
                p_parcial: '0%',
                pa_realizados: 0,
                pa_no_realizados: 0
            },
            inspectionsType2: {
                inspection: {
                    labels: [],
                    datasets: []
                },
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
                }
            },
            complianceType2: {
                inspection: {
                    labels: [],
                    datasets: []
                },
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
                }
            }
        }
    },
    created() {
        this.updateTotales()
        this.fetchSelect('selectBar', '/selects/multiselectBarInspection')
        this.fetch()
    },
    computed: {
        inspectionData: function() {
            return this.inspections[this.inspectionsSelected]
        },
        complianceData: function() {
            return this.compliance[this.inspectionsSelected]
        },
        inspectionType2Data: function() {
            return this.inspectionsType2[this.inspectionsSelectedType2]
        },
        complianceType2Data: function() {
            return this.complianceType2[this.inspectionsSelectedType2]
        },
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

                axios.post('/industrialSecurity/dangerousConditions/inspection/reportDinamic', postData)
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
                if (this[key]) {
                    this[key] = value;
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