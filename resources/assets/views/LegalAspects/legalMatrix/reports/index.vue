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

                                    <div class="float-right" style="padding-right: 10px;">
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
                                <div><b>% Artículos Incumplimiento:</b> {{resumenFulfillment.percentage_nc}}</div>
                            </b-col>
                        </b-row>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="primary" title="Cumplimiento de Artículos" class="mb-3 box-shadow-none">
                        <b-row>
                            <div class="col-md-4" style="padding-bottom: 15px;">
                                <div class="table-responsive" v-if="Object.keys(fulfillmentData.datasets.count).length > 0">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">Sistema</th>
                                                <th class="text-center align-middle">Total</th>                                                   
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in fulfillmentData.datasets.count" :key="`system-${index}`">
                                                <td class="align-middle">{{ index }}</td>
                                                <td class="text-center align-middle">{{ row }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <chart-bar-multiple
                                    :chart-data="fulfillmentData"
                                    title="Cumplimiento de Artículos"
                                    ref="fulfillment"/>
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

export default {
    name: 'legalaspects-lm-law-report',
    metaInfo: {
        title: 'Matriz Legal - Reporte'
    },
    components:{
        Loading,
        FilterGeneral,
        ChartBarMultiple
    },
    data () {
        return {
            filters: [],
            isLoading: false,
            data: [],
            fulfillment: {
                labels: [],
                datasets: {
                    count: []
                }
            },
            resumenFulfillment: {
                total_laws: '',
                total_articles: '',
                articles_t: '',
                articles_c: '',
                articles_nc: '',
                percentage_c: '',
                percentage_nc: ''
            },
            helps: {
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
                    text: '1. Parcial (0.5)\n2. En estudio (1)\n3. No cumple (1)\n4. Sin calificar(1)',
                }
            }
        }
    },
    created(){
        this.fetch()
    },
    computed: {
        fulfillmentData: function() {
            return this.fulfillment
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
            if (!this.isLoading)
            {
                this.isLoading = true;

                axios.post('/legalAspects/legalMatrix/law/report', this.filters)
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
        }
    }
}

</script>