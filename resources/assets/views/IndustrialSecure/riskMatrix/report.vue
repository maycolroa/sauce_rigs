<template>
    <div>
        <header-module
            title="MATRIZ DE PELIGROS"
            subtitle="REPORTE"
            url="industrialsecure-riskmatrix"
        />
        <loading :display="isLoading"/>
        <div v-show="!isLoading">
            <b-row align-h="end">
                <b-col>
                    <b-btn v-if="auth.can['riskMatrix_c']" @click="exportReport()" variant="primary"><i class="fas fa-download"></i> &nbsp; Exportar Reporte Excel</b-btn>
                    <b-btn v-if="auth.can['riskMatrix_c']" @click="exportReportPdf()" variant="primary"><i class="fas fa-download"></i> &nbsp; Exportar Reporte PDF</b-btn>
                    <b-btn :to="{name:'industrialsecure-riskmatrix-report-history'}" variant="primary">Ver historial</b-btn>
                </b-col>
                <b-col cols="3">
                    <filter-danger-matrix-report 
                        v-model="filters" 
                        configName="industrialsecure-riskmatrix-report" 
                        :isDisabled="isLoading"/>
                </b-col>
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
                                            <b-btn @click="fetchTable(col.row, col.col, col.label, col.count)" style="width: 100%;" :variant="col.color">{{ col.label }} <b-badge variant="light">{{ col.count }}</b-badge></b-btn>
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
                    <b-card border-variant="secondary" :title="titleTable" class="mb-3 box-shadow-none" v-if="showTableRisk" :key="keyTableRisk">
                        <vue-table
                            ref="tableRisk"
                            configName="industrialsecure-riskmatrix-report"
                            :customColumnsName="true"
                            :params="paramsTable"
                            ></vue-table>
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
                                            <b-btn @click="fetchTableResidual(col.row, col.col, col.label, col.count)" style="width: 100%;" :variant="col.color">{{ col.label }} <b-badge variant="light">{{ col.count }}</b-badge></b-btn>
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
                    <b-card border-variant="secondary" :title="titleTableResidual" class="mb-3 box-shadow-none" v-if="showTableRiskResidual" :key="keyTableRiskResidual">
                        <vue-table
                            ref="tableRisk"
                            configName="industrialsecure-riskmatrix-report-residual"
                            :customColumnsName="true"
                            :params="paramsTableResidual"
                            ></vue-table>
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
                <b-btn variant="default" :to="{name: 'industrialsecure-riskmatrix'}">Atras</b-btn>
            </template>
        </div>
    </diV>
</template>

<script>

import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";
import FilterDangerMatrixReport from '@/components/Filters/FilterDangerMatrixReport.vue';

export default {
    name: 'industrialsecure-riskmatrix-report',
    metaInfo: {
        title: 'Matriz de Riesgos - Reporte'
    },
    components:{
        Loading,
        FilterDangerMatrixReport
    },
    data () {
        return {
            filters: [],
            isLoading: false,
            showTableRisk: false,
            showTableRiskResidual: false,
            keyTableRisk: '',
            titleTable: '',
            paramsTable: {},
            data: [],
            dataResidual: [],
            reportTableResidual: [],
            typeParams: 'filters',
            filtersTable: {
                rowI: '',
                colI: '',
                rowR: '',
                colR: ''
            }
        }
    },
    created(){
        this.fetch()
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
        }
    },
    methods: {
        fetch()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;
                this.clearAttrTable()

                axios.post('/industrialSecurity/risksMatrix/report', this.filters)
                .then(response => {
                    this.data = response.data.data;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });

                axios.post('/industrialSecurity/risksMatrix/reportResidual', this.filters)
                .then(response => {
                    this.dataResidual = response.data.data;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });

                axios.post('/industrialSecurity/risksMatrix/reportTableResidual', this.filters)
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
        fetchTable(row, col, label, count)
        {
            this.filtersTable.rowI = row
            this.filtersTable.colI = col

            this.clearAttrTable()

            if (count > 0)
            {
                this.$set(this.paramsTable, 'rowI', row)
                this.$set(this.paramsTable, 'colI', col)
                this.$set(this.paramsTable, 'labelI', label)
                
                _.forIn(this.filters, (value, key) => {
                    this.$set(this.paramsTable, key, value)
                });
                
                this.titleTable = `Riesgos ${row} - ${col}`
                this.typeParams = 'paramsTable'
                this.showTableRisk = true
            }
        },
        fetchTableResidual(row, col, label, count)
        {
            this.filtersTable.rowR = row
            this.filtersTable.colR = col

            this.clearAttrTableResidual()

            if (count > 0)
            {
                this.$set(this.paramsTableResidual, 'rowR', row)
                this.$set(this.paramsTableResidual, 'colR', col)
                this.$set(this.paramsTableResidual, 'labelR', label)
                
                _.forIn(this.filters, (value, key) => {
                    this.$set(this.paramsTableResidual, key, value)
                });
                
                this.titleTableResidual = `Riesgos ${row} - ${col}`
                this.typeParams = 'paramsTableResidual'
                this.showTableRiskResidual = true
            }
        },
        clearAttrTable()
        {
            this.showTableRisk = false
            this.titleTable = ''
            this.paramsTable = {}
            this.keyTableRisk = new Date().getTime() + Math.round(Math.random() * 10000)
            this.typeParams = 'filters'
        },
        clearAttrTableResidual()
        {
            this.showTableRiskResidual = false
            this.titleTableResidual = ''
            this.paramsTableResidual = {}
            this.keyTableRiskResidual = new Date().getTime() + Math.round(Math.random() * 10000)
            this.typeParams = 'filters'
        },
        exportReport() {
            this.postData = Object.assign({}, {filtersTable: this.filtersTable}, this[this.typeParams]);
            axios.post('/industrialSecurity/risksMatrix/reportExport', this.postData)
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        exportReportPdf() {
            this.postData = Object.assign({}, {filtersTable: this.filtersTable}, this[this.typeParams]);

            axios.post('/industrialSecurity/risksMatrix/reportExportPdf', this.postData)
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
    }
}

</script>