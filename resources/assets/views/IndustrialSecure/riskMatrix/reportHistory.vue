<template>
    <div>
        <header-module
            title="MATRIZ DE PELIGROS"
            subtitle="REPORTE HISTORICO"
            url="industrialsecure-dangermatrix-report"
        />
        <loading :display="isLoading"/>
        <div v-show="!isLoading">
            <b-row align-h="end">
                <b-col>
                    <b-btn v-if="auth.can['riskMatrix_c']" @click="exportReport()" variant="primary"><i class="fas fa-download"></i> &nbsp; Exportar Reporte</b-btn>
                </b-col>
                <b-col cols="3">
                    <filter-danger-matrix-report-history
                        ref="filter"
                        :key="`filter-${keyFilter}`"
                        v-model="filters" 
                        configName="industrialsecure-riskmatrix-report-history" 
                        :isDisabled="isLoading"
                        :year="year"
                        :month="month"/>
                </b-col>
            </b-row>
            <b-row>
                <vue-ajax-advanced-select :disabled="isLoading" class="col-md-6" v-model="year" name="year" label="Año" placeholder=Año :url="urlMultiselect" :parameters="{column: 'year'}">
                </vue-ajax-advanced-select>
                <vue-ajax-advanced-select :disabled="isLoading || !year" class="col-md-6" v-model="month" name="month" label="Mes" placeholder="Mes" :url="urlMultiselect" :parameters="{column: 'month', year: year}" :emptyAll="empty.month" @updateEmpty="updateEmptyKey('month')">
                    </vue-ajax-advanced-select>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="secondary" title="Mapa Riesgos Inherentes" class="mb-3 box-shadow-none">
                        <div v-if="showLabelCol">
                            <p class="text-center align-middle"><b>Eje Y: Impacto / Eje X: Frecuencia</b></p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th v-if="showLabelCol"> </th>
                                        <th v-for="(header, index) in headers" :key="index" class="text-center align-middle">
                                            {{index + 1}}.{{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in information" :key="index">
                                        <th v-if="showLabelCol" class="text-center align-middle">{{information.length - index}}.{{ row[0].col }}</th>
                                        <td v-for="(col, index2) in row" :key="index2" :class="`bg-${col.color}`">
                                            <b-btn style="width: 100%;" :variant="col.color">{{ col.label }} <b-badge variant="light">{{ col.count }}</b-badge></b-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="secondary" title="Mapa Riesgos Residuales" class="mb-3 box-shadow-none">
                        <div v-if="showLabelCol">
                            <p class="text-center align-middle"><b>Eje Y: Impacto / Eje X: Frecuencia</b></p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th v-if="showLabelCol"> </th>
                                        <th v-for="(header, index) in headersResidual" :key="index" class="text-center align-middle">
                                            {{index + 1}}.{{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in informationResidual" :key="index">
                                        <th v-if="showLabelCol" class="text-center align-middle">{{informationResidual.length - index}}.{{ row[0].col }}</th>
                                        <td v-for="(col, index2) in row" :key="index2" :class="`bg-${col.color}`">
                                            <b-btn style="width: 100%;" :variant="col.color">{{ col.label }} <b-badge variant="light">{{ col.count }}</b-badge></b-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="secondary" title="" class="mb-3 box-shadow-none">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ keywordCheck('process') }}</th>
                                        <th>{{ keywordCheck('area') }}</th>
                                        <th>Riesgo</th>
                                        <th>Evento de riesgo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in tableResidual" :key="index">
                                        <td>{{ row.process }}</td>
                                        <td>{{ row.area }}</td>
                                        <td :class="`bg-${row.risk['color']}`">R.{{ row.risk['sequence'] }}</td>
                                        <td>{{ row.risk_name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <div class="row float-right pt-10 pr-15">
            <template>
                <b-btn variant="default" :to="{name: 'industrialsecure-dangermatrix-report'}">Atras</b-btn>
            </template>
        </div>
    </diV>
</template>

<script>

import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";
import FilterDangerMatrixReportHistory from '@/components/Filters/FilterDangerMatrixReportHistory.vue';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
    name: 'industrialsecure-dangermatrix-report-history',
    metaInfo: {
        title: 'Matriz de Peligros - Reporte Historico'
    },
    components:{
        Loading,
        FilterDangerMatrixReportHistory,
        VueAjaxAdvancedSelect
    },
    data () {
        return {
            filters: [],
            isLoading: false,
            data: [],
            dataResidual: [],
            reportTableResidual: [],
            typeParams: 'filters',
            year: '',
            month: '',
            empty: {
                month: false
            },
            urlMultiselect: '/selects/rmReportMultiselect',
            keyFilter: true
        }
    },
    created(){
        //this.fetch()
    },
    computed: {
        headers() {
            if (Object.keys(this.data).length > 0)
            {
                return this.data.headers;
            }

            return [];
        },
        headersResidual() {
            if (Object.keys(this.dataResidual).length > 0)
            {
                return this.dataResidual.headers;
            }

            return [];
        },
        information() {
            if (Object.keys(this.data).length > 0)
            {
                return this.data.data
            }

            return []
        },
        informationResidual() {
            if (Object.keys(this.dataResidual).length > 0)
            {
                return this.dataResidual.data
            }

            return []
        },
        tableResidual() {
            if (Object.keys(this.reportTableResidual).length > 0)
            {
                return this.reportTableResidual.data
            }

            return []
        },
        showLabelCol() {
            if (Object.keys(this.data).length > 0)
            {
                return this.data.showLabelCol;
            }

            return false;
        }
    },
    watch: {
        filters: {
            handler(val){
                this.fetch()
            },
            deep: true
        },
        'year'() {
            this.emptySelect('month', 'month')
        },
        'month'() {
            if (this.year && this.month)
            {
                this.keyFilter = !this.keyFilter
                this.$refs.filter.cleanFilters()
                this.fetch()
            }
        }
    },
    methods: {
        fetch()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;
                this.postData = Object.assign({}, {year: this.year}, {month: this.month}, this.filters);

                axios.post('/industrialSecurity/risksMatrix/reportHistory', this.postData)
                .then(response => {
                    this.data = response.data.data;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });

                axios.post('/industrialSecurity/risksMatrix/reportHistoryResidual', this.postData)
                .then(response => {
                    this.dataResidual = response.data.data;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });

                axios.post('/industrialSecurity/risksMatrix/reportHistoryTableResidual', this.postData)
                .then(response => {
                    this.reportTableResidual = response.data.data;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });
            }
        },
        exportReport() {
            this.postData = Object.assign({}, {year: this.year}, {month: this.month}, this.filters);

            axios.post('/industrialSecurity/risksMatrix/reportHistoryExport', this.postData)
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        emptySelect(keySelect, keyEmpty)
        {
            if (this[keySelect] !== '')
            {
                this.empty[keyEmpty] = true
                this[keySelect] = ''
            }
        },
        updateEmptyKey(keyEmpty)
        {
            this.empty[keyEmpty]  = false
        },
    }
}

</script>