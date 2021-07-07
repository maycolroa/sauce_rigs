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
                    <b-btn v-if="auth.can['dangerMatrix_export_report']" @click="exportReport()" variant="primary"><i class="fas fa-download"></i> &nbsp; Exportar Reporte</b-btn>
                    <b-btn :to="{name:'industrialsecure-dangermatrix-report-history'}" variant="primary">Ver historial</b-btn>
                </b-col>
                <b-col cols="3">
                    <filter-danger-matrix-report 
                        v-model="filters" 
                        configName="industrialsecure-dangermatrix-report" 
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
            typeParams: 'filters'
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
            if (Object.keys(this.data).length > 0)
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
            if (Object.keys(this.data).length > 0)
            {
                return this.dataResidual.data
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
            }
        },
        fetchTable(row, col, label, count)
        {
            this.clearAttrTable()

            if (count > 0)
            {
                this.$set(this.paramsTable, 'row', row)
                this.$set(this.paramsTable, 'col', col)
                this.$set(this.paramsTable, 'label', label)
                
                _.forIn(this.filters, (value, key) => {
                    this.$set(this.paramsTable, key, value)
                });
                
                this.titleTable = `Riesgos`
                this.typeParams = 'paramsTable'
                this.showTableRisk = true
            }
        },
        fetchTableResidual(row, col, label, count)
        {
            this.clearAttrTableResidual()

            if (count > 0)
            {
                this.$set(this.paramsTableResidual, 'row', row)
                this.$set(this.paramsTableResidual, 'col', col)
                this.$set(this.paramsTableResidual, 'label', label)
                
                _.forIn(this.filters, (value, key) => {
                    this.$set(this.paramsTableResidual, key, value)
                });
                
                this.titleTableResidual = `Riesgos`
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
            axios.post('/industrialSecurity/risksMatrix/reportExport', this[this.typeParams])
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        }
    }
}

</script>