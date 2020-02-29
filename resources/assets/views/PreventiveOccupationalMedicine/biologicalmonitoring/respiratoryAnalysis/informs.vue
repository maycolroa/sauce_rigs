<template>
    <div>
        <header-module
            title="ANÁLISIS RESPIRATORIO"
            subtitle="INFORMES"
            url="biologicalmonitoring-respiratoryanalysis"
        />
        <div>
            <filter-general 
                v-model="filters" 
                configName="biologicalmonitoring-respiratoryAnalysis" 
                :isDisabled="isLoading"/>
        </div>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Colaboradores con deficiencias respiratorias" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col>
                            <b>Total de Colaboradores con deficiencias respiratorias {{ breathingProblems.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <br>
                    <chart-pie 
                        :chart-data="breathingProblems"
                        title="Colaboradores con deficiencias respiratorias"
                        color-line="red"
                        ref="breathingProblems"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Resultado de Espirometria" class="mb-3 box-shadow-none">
                    <b-row>
                            <b-col>
                                <b>Total de Resultado de Espirometria {{ classificationAts.datasets.count }} </b>
                            </b-col>
                    </b-row>
                    <br>
                    <chart-pie 
                        :chart-data="classificationAts"
                        title="Resultado de Espirometria"
                        color-line="red"
                        ref="classificationAts"/>
                </b-card>
            </b-col>
        </b-row>

        <!--<b-row>
            <b-col>
                <b-row align-h="end">
                        <b-col cols="6">
                            <b>Total de casos con alguna deficiencia respiratoria según Planta {{ breathingProblemsRegional.datasets.count }} </b>
                        </b-col>
                    </b-row>
                <b-card border-variant="primary" title="Total de casos con alguna deficiencia respiratoria según Planta" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="breathingProblemsRegional"
                        title="Total de casos con alguna deficiencia respiratoria según Planta"
                        color-line="red"
                        ref="breathingProblemsRegional"/>
                </b-card>
            </b-col>
        </b-row>-->

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Clasificación del patrón obstructivo" class="mb-3 box-shadow-none">
                    <b-row>
                            <b-col>
                                <b>Total de Clasificación del patrón obstructivo {{ classificationObstructive.datasets.count }} </b>
                            </b-col>
                    </b-row>
                    <br>
                    <chart-pie
                        :chart-data="classificationObstructive"
                        title="Clasificación del patrón obstructivo"
                        color-line="red"
                        ref="classificationObstructive"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Clasificación del patrón Restrictivo" class="mb-3 box-shadow-none">
                    <b-row>
                            <b-col>
                                <b>Total de Clasificación del patrón Restrictivo {{ classificationRestrictive.datasets.count }} </b>
                            </b-col>
                    </b-row>
                    <br>
                    <chart-pie
                        :chart-data="classificationRestrictive"
                        title="Clasificación del patrón Restrictivo"
                        color-line="red"
                        ref="classificationRestrictive"/>
                </b-card>
            </b-col>
        </b-row>

        <div class="row float-right pt-10 pr-10">
            <template>
                <b-btn variant="default" :to="{name: 'biologicalmonitoring-respiratoryanalysis'}">Atras</b-btn>
            </template>
        </div>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import ChartPie from '@/components/ECharts/ChartPie.vue';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
    name: 'biologicalmonitoring-respiratoryAnalysis-informs',
    metaInfo: {
        title: 'Análisis Respiratorio - Informes'
    },
    components:{
        ChartPie,
        FilterGeneral,
        ChartBar
    },
    data () {
        return {
            filters: [],
            isLoading: false,

            breathingProblems: {
                labels: [],
                datasets: []
            },
            classificationAts: {
                labels: [],
                datasets: []
            },
            classificationObstructive: {
                labels: [],
                datasets: []
            },
            classificationRestrictive: {
                labels: [],
                datasets: []
            },
            breathingProblemsRegional: {
                labels: [],
                datasets: []
            }
        }
    },
    created(){
        this.fetch()
    },
    computed: {
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
                //console.log('buscando...')
                this.isLoading = true;

                axios.post('/biologicalmonitoring/respiratoryAnalysis/informs', this.filters)
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
                if (this[key]) {
                    this[key] = value;
                }
            });
        }
    }
}
</script>