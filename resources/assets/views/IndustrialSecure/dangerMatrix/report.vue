<template>
    <div>
        <h4 class="font-weight-bold mb-4">
            <span class="text-muted font-weight-light">Matriz de Peligros /</span> Reporte
        </h4>
        <loading :display="isLoading"/>
        <div v-show="!isLoading">
            <b-row align-h="end">
                <b-col cols="1">
                    <filter-danger-matrix-report 
                        v-model="filters" 
                        configName="industrialsecure-dangermatrix-report" 
                        :isDisabled="isLoading"/>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card border-variant="secondary" title="" class="mb-3 box-shadow-none">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th v-for="(header, index) in headers" :key="index" class="text-center align-middle">
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in information" :key="index">
                                        <th class="text-center align-middle" scope="row">{{ row.key }}</th>
                                        <td v-for="(col, index2) in row.data" :key="index2" :class="`bg-${col.color}`">
                                            <b-btn @click="fetchTable(row.key, index2, col.label, col.count)" style="width: 100%;" :variant="col.color">{{ col.label }} <b-badge variant="light">{{ col.count }}</b-badge></b-btn>
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
                            configName="industrialsecure-dangermatrix-report"
                            :params="paramsTable"
                            ></vue-table>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <div class="row float-right pt-10 pr-15">
            <template>
                <b-btn variant="default" :to="{name: 'industrialsecure-dangermatrix'}">Atras</b-btn>
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
    name: 'industrialsecure-dangermatrix-report',
    metaInfo: {
        title: 'Matriz de Peligros - Reporte'
    },
    components:{
        Loading,
        FilterDangerMatrixReport
    },
    data () {
        return {
            filters: [],
            isLoading: false,
            showTableDanger: false,
            keyTableDanger: '',
            titleTable: '',
            paramsTable: {},
            data: [],
        }
    },
    created(){
        this.fetch()
    },
    computed: {
        headers() {
            if (Object.keys(this.data).length > 0)
            {
                let headers = [];

                _.forIn(this.data[Object.keys(this.data)[0]], (value, key) => {
                    headers.push(key);
                });

                return headers;
            }

            return [];
        },
        information() {
            if (Object.keys(this.data).length > 0)
            {
                let information = []

                Object.keys(this.data).sort().reverse()
                    .forEach(key => {
                        information.push( {
                            'key': key, 
                            'data': this.data[key]
                        })
                    })

                return information
            }

            return []
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

                axios.post('/industrialSecurity/dangersMatrix/report', this.filters)
                .then(response => {
                    this.data = response.data;
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
                
                this.titleTable = `Peligros ${label} de (${row} - ${col})`
                this.showTableDanger = true
            }
        },
        clearAttrTable()
        {
            this.showTableDanger = false
            this.titleTable = ''
            this.paramsTable = {}
            this.keyTableDanger = new Date().getTime() + Math.round(Math.random() * 10000)
        }
    }
}

</script>