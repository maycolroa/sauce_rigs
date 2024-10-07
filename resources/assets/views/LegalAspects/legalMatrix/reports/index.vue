<template>
    <div>
        <header-module
            title="MATRIZ LEGAL"
            subtitle="REPORTE"
            url="legalaspects-legalmatrix"
        />
        <loading :display="isLoading"/>
        <div v-show="!isLoading">
            <b-row align-h="end">
                <b-col>
                    <b-btn v-if="auth.can['laws_report_export']" @click="exportReport()" variant="primary"><i class="fas fa-download"></i> &nbsp; Exportar Reporte</b-btn>
                </b-col>
                <b-col cols="3">
                    <filter-general
                        v-model="filters" 
                        configName="legalAspects-legalMatrix-law-qualify" 
                        :isDisabled="isLoading"/>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="primary" title="Resumen" class="mb-3 box-shadow-none">
                        <b-row>
                            <b-col>
                                <div><b>Número de Normas:</b> {{resumenFulfillment.total_laws}}</div>
                                <div><b>Número de Artículos:</b> {{resumenFulfillment.total_articles}}</div>
                            </b-col>
                            <b-col>
                                <div><b>Total de artículos que aplican para el cumplimiento:</b> {{resumenFulfillment.articles_t}}

                                    <!--<div class="float-right" style="padding-right: 10px;">
                                        <b-btn v-b-popover.hover.focus.left="helps.articles_t.text" :title="helps.articles_t.title" variant="secondary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                                    </div>
                                </div>
                                <div><b># Artículos Cumplimiento:</b> {{resumenFulfillment.articles_c}} 
                                
                                    <div class="float-right" style="padding-right: 10px;">
                                        <b-btn v-b-popover.hover.focus.left="helps.articles_c.text" :title="helps.articles_c.title" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                                    </div>
                                </div>
                                <div><b># Artículos Incumplimiento:</b> {{resumenFulfillment.articles_nc}}

                                    <div class="float-right" style="padding-right: 10px;">
                                        <b-btn v-b-popover.hover.focus.left="helps.articles_nc.text" :title="helps.articles_nc.title" variant="secondary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                                    </div>
                                </div>
                                <div><b>% Artículos Cumplimiento:</b> {{resumenFulfillment.percentage_c}}</div>
                                <div><b>% Artículos Incumplimiento:</b> {{resumenFulfillment.percentage_nc}}</div>-->
                                </div>
                            </b-col>
                        </b-row>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="primary" title="" class="mb-3 box-shadow-none">
                        <b-row>
                            <div class="col-md-12" style="padding-bottom: 15px;">
                                <b-col>
                                    <vue-advanced-select :disabled="isLoading" v-model="legalMatrixSelected" :options="selectBar" :searchable="true" name="legalMatrixSelected" label="Categoría">
                                    </vue-advanced-select>
                                </b-col>
                            </div>
                        </b-row>
                        <b-row>
                            <div class="col-md-12" style="padding-bottom: 15px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">Categoría</th>
                                                <th class="text-center align-middle">Cumple</th>
                                                <th class="text-center align-middle">No cumple</th>
                                                <th class="text-center align-middle">Parcial</th>
                                                <th class="text-center align-middle">En estudio</th>
                                                <th class="text-center align-middle">No aplica</th>
                                                <th class="text-center align-middle">Informativo</th>
                                                <th class="text-center align-middle">Sin calificar</th>   
                                                <th class="text-center align-middle">No vigente</th>
                                                <th class="text-center align-middle">En Transición</th>
                                                <th class="text-center align-middle">Pendiente reglamentación</th>    
                                                <th class="text-center align-middle">Total</th>    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in reportTableDinamic" :key="`row-${index}`">
                                                <td class="align-middle">{{ row.category }}</td>
                                                <td class="text-center align-middle">{{ row["Cumple"] }}</td>
                                                <td class="text-center align-middle">{{ row["No cumple"] }}</td>
                                                <td class="text-center align-middle">{{ row["Parcial"] }}</td>
                                                <td class="text-center align-middle">{{ row["En estudio"] }}</td>
                                                <td class="text-center align-middle">{{ row["No aplica"] }}</td>
                                                <td class="text-center align-middle">{{ row["Informativo"] }}</td>
                                                <td class="text-center align-middle">{{ row["Sin calificar"] }}</td>
                                                 <td class="text-center align-middle">{{ row["No vigente"] }}</td>
                                                <td class="text-center align-middle">{{ row["En Transición"] }}</td>
                                                <td class="text-center align-middle">{{ row["Pendiente reglamentación"] }}</td>
                                                <td class="text-center align-middle">{{ row["Total"] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </b-row>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="primary" title="Cumplimiento de Artículos" class="mb-3 box-shadow-none">
                        <b-row>
                            <div class="col-md-10">
                                <chart-bar-multiple
                                    :chart-data="fulfillmentData"
                                    title="Cumplimiento de Artículos"
                                    ref="fulfillment"/>
                            </div>
                        </b-row>
                    </b-card>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <b-card border-variant="primary" class="mb-3 box-shadow-none">
                        <div class="col-md-10">
                            <b-row>                            
                                    <b-col>
                                        <table-report
                                            :rows="fulfillmentPie.datasets.table"
                                        />
                                    </b-col>
                                    <b-col>
                                    <chart-pie 
                                        :chart-data="fulfillmentPie"
                                        title="Cumplimiento de Artículos"
                                        color-line="red"
                                        :colors="colors"
                                        ref="fulfillmentPie"/>                                
                                    </b-col>
                            </b-row>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
            <b-row v-if="auth.legalMatrixRisk == 'SI' && auth.hasRole['Superadmin']">
                <b-col>
                    <b-card border-variant="primary" title="" class="mb-3 box-shadow-none">
                        <b-row>
                            <div class="col-md-12" style="padding-bottom: 15px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">Sistema que aplica</th>
                                                <th class="text-center align-middle">Riesgos</th>
                                                <th class="text-center align-middle">Oportunidades</th>
                                                <th class="text-center align-middle">No aplica</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in reportTableRisk" :key="`row-${index}`">
                                                <td class="align-middle">{{ row.category }}</td>
                                                <td class="text-center align-middle">{{ row["count_risk"] }}</td>
                                                <td class="text-center align-middle">{{ row["count_opport"] }}</td>
                                                <td class="text-center align-middle">{{ row["count_n_a"] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';
import ChartBarMultiple from '@/components/ECharts/ChartBarMultiple.vue';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import ChartPie from '@/components/ECharts/ChartPieDinamicColors.vue';
import TableReport from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/TableReport.vue';

export default {
    name: 'legalaspects-lm-law-report',
    metaInfo: {
        title: 'Matriz Legal - Reporte'
    },
    components:{
        Loading,
        FilterGeneral,
        ChartBarMultiple,
        VueAdvancedSelect,
        ChartPie,
        TableReport
    },
    data () {
        return {
            filters: [],
            selectBar: [],
            isLoading: false,
            fulfillment: {
                labels: [],
                datasets: {
                    count: []
                }
            },
            fulfillmentPie: {
                labels: [],
                datasets: {
                    table: [],
                    data: [],
                    count: []
                }
            },
            colors: [],
            legalMatrixSelected: 'systemApply',
            reportTableDinamic: [],
            reportTableRisk: [],
            resumenFulfillment: {
                total_laws: '',
                total_articles: '',
                articles_t: '',
                articles_c: '',
                articles_nc: '',
                percentage_c: '',
                percentage_nc: ''
            },
            /*helps: {
                articles_t: {
                    title: 'Artículos que aplican',
                    text: '1. Parcial\n2. En estudio\n3. No cumple\n4. Cumple\n5. Sin calificar',
                },
                articles_c: {
                    title: 'Artículos / Puntos',
                    text: '1. Parcial (0.5)\n2. Cumple (1)\n',
                },
                articles_nc: {
                    title: 'Artículos / Puntos',
                    text: '1. En estudio (1)\n2. No cumple (1)\n3. Sin calificar(1)',
                }
            }*/
        }
    },
    created(){
        this.fetchSelect('selectBar', '/selects/multiselectBarLegalMatrix')
        this.fetch()
    },
    computed: {
        fulfillmentData: function() {
            return this.fulfillment
        },
        legalMatrixData: function() {
            return this.legalMatrix[this.legalMatrixSelected]
        }
    },
    watch: {
        filters: {
            handler(val){
                this.fetch()
            },
            deep: true
        },
        legalMatrixSelected() {
            this.fetch();
        }
    },
    methods: {
        fetch()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;

                let postData = Object.assign({}, {legalMatrixSelected: this.legalMatrixSelected}, this.filters);

                axios.post('/legalAspects/legalMatrix/law/report', postData)
                .then(response => {
                    this.update(response);
                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });
            }
        },
        update(response) {
            _.forIn(response.data, (value, key) => {
                if (this[key]) {
                    this[key] = value;
                }
            });
        },
        exportReport() {
            axios.post('/legalAspects/legalMatrix/law/report/export', this.filters)
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
                this.$router.go(-1);
            });
        },
    }
}

</script>