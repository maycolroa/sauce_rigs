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
                <div>
                    <filter-general 
                        v-model="filters" 
                        configName="industrialsecure-accidents-report" />
                </div>
                <b-row>
                    <b-card style="width:95%">
                        <b-row>
                            <b-col>
                                <h4>Empleados con más accidentes relacionados</h4>
                                <chart-bar                                    
                                    :chart-data="reporEmployees"
                                    title="Empleados con más accidentes relacionados"
                                    color-line="red"
                                    ref=""
                                />
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <h4>Mecanismos con más accidentes relacionados</h4>
                                <chart-bar                                    
                                    :chart-data="reportMechanism"
                                    title="Mecanismos con más accidentes relacionados"
                                    color-line="red"
                                    ref=""
                                />
                            </b-col>
                        </b-row>
                        <b-row style="width:95%;">
                            <b-col>
                                <h4>Reporte Dinámico</h4>
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
                            <p>Para ocultar algun año haga click en el año en la leyenda de la grafica</p>
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
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
    name: 'legalaspects-informs-report',
    metaInfo: {
        title: 'Informes Mensuales - Reporte'
    },
    components:{
        Loading,
        VueAdvancedSelect,
        LineComponent,
        ChartBar,
        FilterGeneral
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
            filters: [],
            reportMechanism: {
                labels: [],
                datasets: []
            },
            reporEmployees: {
                labels: [],
                datasets: []
            },
        }
    },
    watch: {
        filters: {
            handler(val){
                this.fetch()
                this.fetch2()
            },
            deep: true
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
            this.postData = Object.assign({}, {option: this.option}, {filters: this.filters});

            axios.post('/industrialSecurity/accidents/reportLine', this.postData)
                .then(response => {
                this.report_line = response.data

                this.chartData = {
                    labels: response.data.headings,
                    datasets: response.data.answers
                }
  
                setTimeout(() => {
                    this.test = !this.test;
                }, 1500);

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

                let postData = Object.assign({}, {filters: this.filters});

                axios.post('/industrialSecurity/accidents/reportDinamic', postData)
                .then(data => {
                    this.update(data);
                    this.fetch3()
                    this.fetch4()
                    this.isLoading = false;
                })
                .catch(error => {
                    console.log(error);
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });
            }
        },
        fetch3()
        {
            let postData = Object.assign({}, {filters: this.filters});
            axios.post('/industrialSecurity/accidents/reportMechanism', postData)
            .then(data => {
                this.reportMechanism = data.data;
            })
            .catch(error => {
                console.log(error);
                this.isLoading = false;
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
        },
        fetch4()
        {
            let postData = Object.assign({}, {filters: this.filters});

            axios.post('/industrialSecurity/accidents/reportEmployee', postData)
            .then(data => {
                this.reporEmployees = data.data;
            })
            .catch(error => {
                console.log(error);
                this.isLoading = false;
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
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