<template>
    <div>
        <header-module
            title="INVESTIGACIÓN DE ACCIDENTES E INCIDENTES DE TRABAJO"
            subtitle="REPORTE"
            url="industrialsecure-accidentswork"
        />
        
        <loading :display="isLoading"/>
        <div style="width:100%" class="col-md" v-show="!isLoading">
            <b-card no-body>
                <b-row>
                    <b-card style="width:95%">
                        <b-row style="width:95%;">
                            <b-col>
                               <vue-advanced-select class="col-md-6" v-model="option" :multiple="false" :options="options" :hide-selected="false" @input="fetch" name="option" label="Opción a graficar" placeholder="Seleccione una opción">
                                </vue-advanced-select>
                            </b-col>
                        </b-row>
                        <b-row class="col-md-12">
                            <b-col v-if="option">
                                <line-component v-if="report_line.answers.length > 0" :key="test" :chartData="chartData" ref="line"></line-component>
                                <b-container v-else>
                                    <b-row align-h="center">
                                        <b-col cols="6">No hay resultados</b-col>
                                    </b-row>
                                </b-container>
                            </b-col>
                        </b-row>
                        <b-row style="width:95%;">
                            <b-col v-show="option">
                                <vue-advanced-select :disabled="isLoading" v-model="category" :options="selectBar" :allowEmpty="false" :searchable="true" name="category" label="Categoria">
                                </vue-advanced-select>
                            </b-col>
                        </b-row>
                        <b-row v-if="category">
                            <b-col>
                                <chart-bar                                    
                                    :chart-data="reportData"
                                    title="Número de accidentes"
                                    color-line="red"
                                    ref=""
                                />
                            </b-col>
                        </b-row>
                    </b-card>
                </b-row>
            </b-card>
        </div>
    </diV>
</template>

<script>

import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import LineComponent from '@/components/Chartjs/ChartLine.vue';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
    name: 'legalaspects-informs-report',
    metaInfo: {
        title: 'Informes Mensuales - Reporte'
    },
    components:{
        Loading,
        VueAdvancedSelect,
        LineComponent,
        ChartBar
    },
    data () {
        return {
            isLoading: false,
            chartData: {},
            postData: {},
            report_line: {
                headings: [],
                answers: []
            },
            options: [
					{ name:'Número de accidentes', value:'accidents'},
					{ name:'Número de personas', value:'persons'}
			],  
            option: '',
            test: true,
            accidents: {
                persons: {
                    sexo: {
                        labels: [],
                        datasets: []
                    },
                    cargo: {
                        labels: [],
                        datasets: []
                    },
                    departament: {
                        labels: [],
                        datasets: []
                    },
                    city: {
                        labels: [],
                        datasets: []
                    },
                    causo_muerte: {
                        labels: [],
                        datasets: []
                    },
                    mecanismo: {
                        labels: [],
                        datasets: []
                    },
                    sitio: {
                        labels: [],
                        datasets: []
                    },
                    agente: {
                        labels: [],
                        datasets: []
                    },
                    part_body: {
                        labels: [],
                        datasets: []
                    },
                    lesion_type: {
                        labels: [],
                        datasets: []
                    }
                },
                accidents: {
                    sexo: {
                        labels: [],
                        datasets: []
                    },
                    cargo: {
                        labels: [],
                        datasets: []
                    },
                    departament: {
                        labels: [],
                        datasets: []
                    },
                    city: {
                        labels: [],
                        datasets: []
                    },
                    causo_muerte: {
                        labels: [],
                        datasets: []
                    },
                    mecanismo: {
                        labels: [],
                        datasets: []
                    },
                    sitio: {
                        labels: [],
                        datasets: []
                    },
                    agente: {
                        labels: [],
                        datasets: []
                    },
                    part_body: {
                        labels: [],
                        datasets: []
                    },
                    lesion_type: {
                        labels: [],
                        datasets: []
                    }
                }
            },
            category: 'departament',
            selectBar: [],
            test2: true,
        }
    },
    computed: {
        reportData: function() {
            if (this.option)
                return this.accidents[this.option][this.category]
        },
        personData: function() {
            return this.accidents.persons[this.category]
        }
    },
    created() {

        this.fetchSelect('selectBar', '/selects/multiselectBarAccident')
        this.fetch2()
    },
    methods: {
        fetch()
        {
            this.postData = Object.assign({}, {option: this.option});

            axios.post('/industrialSecurity/accidents/reportLine', this.postData)
                .then(response => {
                this.report_line = response.data

                this.chartData = {
                    labels: response.data.headings,
                    datasets: response.data.answers
                }

                this.test = !this.test;

                }).catch(error => {
                    console.log(error)
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        fetch2()
        {
            if (!this.isLoading)
            {
                //console.log('buscando...')
                this.isLoading = true;

                let postData = Object.assign({}, this.postData);

                axios.post('/industrialSecurity/accidents/reportDinamic', postData)
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
                if (this.accidents[key]) {
                    this.accidents[key] = value;
                }
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
    }
}

</script>