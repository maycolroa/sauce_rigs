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
                    <b-btn v-if="auth.can['dangerMatrix_export_report']" @click="exportReport()" variant="primary"><i class="fas fa-download"></i> &nbsp; Exportar Reporte</b-btn>
                </b-col>
                <b-col cols="3">
                    <filter-danger-matrix-report-history
                        ref="filter"
                        :key="`filter-${keyFilter}`"
                        v-model="filters" 
                        configName="industrialsecure-dangermatrix-report-history" 
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
                    <b-card border-variant="secondary" title="" class="mb-3 box-shadow-none">
                        <div v-if="showLabelCol">
                            <p class="text-center align-middle"><b>Eje Y: Frecuencia / Eje X: Severidad</b></p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th v-if="showLabelCol"> </th>
                                        <th v-for="(header, index) in headers" :key="index" class="text-center align-middle">
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in information" :key="index">
                                        <th v-if="showLabelCol" class="text-center align-middle">{{ row[0].col }}</th>
                                        <td v-for="(col, index2) in row" :key="index2" :class="`bg-${col.color}`">
                                            <!--<b-btn style="width: 100%;" :variant="col.color">{{ col.label }} <b-badge variant="light">{{ col.count }}</b-badge></b-btn>-->

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
                    <b-card border-variant="secondary" :title="titleTable" class="mb-3 box-shadow-none" v-if="showTableDanger" :key="keyTableDanger">
                        <vue-table
                            ref="tableDanger"
                            configName="industrialsecure-dangermatrix-report-history"
                            :customColumnsName="true"
                            :params="paramsTable"
                            ></vue-table>
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
            typeParams: 'filters',
            year: '',
            month: '',
            empty: {
                month: false
            },
            urlMultiselect: '/selects/dmReportMultiselect',
            keyFilter: true,
            showTableDanger: false,
            keyTableDanger: '',
            titleTable: '',
            paramsTable: {},
            data: [],
            typeParams: 'filters'
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
        information() {
            if (Object.keys(this.data).length > 0)
            {
                return this.data.data
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

                axios.post('/industrialSecurity/dangersMatrix/reportHistory', this.postData)
                .then(response => {
                    this.data = response.data.data;
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

            axios.post('/industrialSecurity/dangersMatrix/reportHistoryExport', this.postData)
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
        clearAttrTable()
        {
            this.showTableDanger = false
            this.titleTable = ''
            this.paramsTable = {}
            this.keyTableDanger = new Date().getTime() + Math.round(Math.random() * 10000)
            this.typeParams = 'filters'
        },
        fetchTable(row, col, label, count)
        {
            this.clearAttrTable()

            if (count > 0)
            {
                this.$set(this.paramsTable, 'row', row)
                this.$set(this.paramsTable, 'col', col)
                this.$set(this.paramsTable, 'label', label)
                this.$set(this.paramsTable, 'year', this.year)
                this.$set(this.paramsTable, 'month', this.month)
                
                _.forIn(this.filters, (value, key) => {
                    this.$set(this.paramsTable, key, value)
                });
                
                this.titleTable = `Peligros ${label} de (${row})`
                this.typeParams = 'paramsTable'
                this.showTableDanger = true
            }
        },
    }
}

</script>