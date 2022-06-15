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
                    <b-card style="width:95%" no-body>
                        <b-row style="width:95%; padding-left: 5%">
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

export default {
    name: 'legalaspects-informs-report',
    metaInfo: {
        title: 'Informes Mensuales - Reporte'
    },
    components:{
        Loading,
        VueAdvancedSelect,
        LineComponent
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
        }
    },
    methods: {
        fetch()
        {
            this.postData = Object.assign({}, {option: this.option});

            axios.post('/industrialSecurity/accidents/reportLine', this.postData)
                .then(response => {
                    console.log(response)
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
        }
    }
}

</script>