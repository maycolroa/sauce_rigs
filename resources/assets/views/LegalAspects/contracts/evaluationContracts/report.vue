<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="REPORTE DE EVALUACIONES REALIZADAS"
        url="legalaspects-evaluations"
    />

    <div class="col-md">
        <b-card no-body>
            <b-card-header class="with-elements">
                <div class="card-title-elements ml-md-auto" v-if="auth.can['contracts_evaluations_report_export']">
                    <b-dd variant="default" :right="isRTL">
                        <template slot="button-content">
                            <span class='fas fa-cogs'></span>
                        </template>
                        <b-dd-item @click="exportReport()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
                    </b-dd>
                </div>
            </b-card-header>
            <b-card-body>
                <div style="padding-top: ">
                    <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
                        <b-row>
                            <b-col>
                                <div><b>Evaluaciones:</b> {{information.evaluations}}</div>
                                <div><b># Cumplimientos:</b> {{information.t_cumple}}</div>
                                <div><b># No Cumplimientos:</b> {{information.t_no_cumple}}</div>
                            </b-col>
                            <b-col>
                                <div><b>% Cumplimientos:</b> {{information.p_cumple}}</div>
                                <div><b>% No Cumplimientos:</b> {{information.p_no_cumple}}</div>
                            </b-col>
                        </b-row>
                    </b-card>
                </div>
                <vue-table
                    configName="legalaspects-evaluations-reports"
                    @filtersUpdate="setFilters"
                    ></vue-table>
            </b-card-body>
        </b-card>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Evaluaciones" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="evaluationsSelected" :options="selectBar" :searchable="true" name="evaluationsSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Número de Evaluaciones</h4>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-12">
                            <chart-bar 
                                :chart-data="evaluationsData"
                                title="Número de evaluaciones realizadas"
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
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import ChartBar from '@/components/ECharts/ChartBar.vue';
import ChartBarCompliance from '@/components/ECharts/ChartBarCompliance.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'legalaspects-evaluations-report',
    metaInfo: {
        title: 'Evaluaciones - Reportes'
    },
    components:{
        VueAdvancedSelect,
        ChartBarCompliance,
        ChartBar
    },
    data () {
        return {
            filters: [],
            selectBar: [],
            isLoading: false,
            information: {
                evaluations: 0,
                t_cumple: 0,
                t_no_cumple: 0,
                p_cumple: '0%',
                p_no_cumple: '0%'
            },
            evaluationsSelected: 'evaluation',
            evaluations: {
                evaluation: {
                    labels: [],
                    datasets: []
                },
                objective: {
                    labels: [],
                    datasets: []
                },
                subobjective: {
                    labels: [],
                    datasets: []
                },
                item: {
                    labels: [],
                    datasets: []
                },
                type_rating: {
                    labels: [],
                    datasets: []
                },
                contract: {
                    labels: [],
                    datasets: []
                }
            },
            compliance: {
                evaluation: {
                    labels: [],
                    datasets: []
                },
                objective: {
                    labels: [],
                    datasets: []
                },
                subobjective: {
                    labels: [],
                    datasets: []
                },
                item: {
                    labels: [],
                    datasets: []
                },
                type_rating: {
                    labels: [],
                    datasets: []
                },
                contract: {
                    labels: [],
                    datasets: []
                }
            }
        }
    },
    created() {
        this.updateTotales()
        this.fetchSelect('selectBar', '/selects/multiselectBarEvaluations')
        this.fetch()
    },
    computed: {
        evaluationsData: function() {
            return this.evaluations[this.evaluationsSelected]
        },
        complianceData: function() {
            return this.compliance[this.evaluationsSelected]
        }
    },
    methods: {
        setFilters(value)
        { 
            this.filters = value
            this.updateTotales()
            this.fetch()
        },
        fetchSelect(key, url)
        {
            GlobalMethods.getDataMultiselect(url)
            .then(response => {
                this[key] = response;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                this.$router.go(-1);
            });
        },
        exportReport() {
            axios.post('/legalAspects/evaluationContract/exportReport', this.filters)
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        updateTotales()
        {
            axios.post('/legalAspects/evaluationContract/getTotales', this.filters)
                .then(response => {
                    this.information = response.data.data;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                    this.$router.go(-1);
                });
        },
        fetch()
        {
            if (!this.isLoading)
            {
                //console.log('buscando...')
                this.isLoading = true;

                axios.post('/legalAspects/evaluationContract/reportDinamic', this.filters)
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
        }
    }
}
</script>