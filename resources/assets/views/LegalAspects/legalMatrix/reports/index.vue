<template>
    <div>
        <h4 class="font-weight-bold mb-4">
            <span class="text-muted font-weight-light">Matriz Legal /</span> Reporte
        </h4>
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
                                <div><b>Puntuación:</b> {{resumenFulfillment.punctuation}}</div>
                                <div><b>Porcentaje de Cumplimiento:</b> {{resumenFulfillment.percentage_fulfillment}}</div>
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
                punctuation: '',
                percentage_fulfillment: ''
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