<template>
    <div>
        <header-module
            title="ANÁLISIS OSTEOMUSCULAR"
            subtitle="INFORMES"
            url="biologicalmonitoring-musculoskeletalanalysis"
        />
        <div>
            <filter-general 
                v-model="filters" 
                configName="biologicalmonitoring-musculoskeletalAnalysis" 
                :isDisabled="isLoading"/>
        </div>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Riesgo Cardiovascular" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="cardiovascularRisk"
                        title="Riesgo Cardiovascular"
                        color-line="red"
                        ref="cardiovascularRisk"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Clasificación Gatiso" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="osteomuscularGroup"
                        title="Clasificación Gatiso"
                        color-line="red"
                        ref="osteomuscularGroup"/>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Clasificación IMC" class="mb-3 box-shadow-none">
                    <chart-pie
                        :chart-data="imcClassification"
                        title="Clasificación IMC"
                        color-line="red"
                        ref="imcClassification"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Clasificación Perimetro" class="mb-3 box-shadow-none">
                    <chart-pie
                        :chart-data="abdominalPerimeterClassification"
                        title="Clasificación Perimetro"
                        color-line="red"
                        ref="abdominalPerimeterClassification"/>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Consolidado Riesgo Personal (Criterio)" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="consolidatedRiskCriterion"
                        title="Consolidado Riesgo Personal (Criterio)"
                        color-line="red"
                        ref="consolidatedRiskCriterion"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Criterio Medico de" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="prioritizationMedicalCriteria"
                        title="Criterio Medico de"
                        color-line="red"
                        ref="prioritizationMedicalCriteria"/>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Habito Ejercicio" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="exerciseHabit"
                        title="Habito Ejercicio"
                        color-line="red"
                        ref="exerciseHabit"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Habito Licor" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="liquorHabit"
                        title="Habito Licor"
                        color-line="red"
                        ref="liquorHabit"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Habito Cigarrillo" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="cigaretteHabit"
                        title="Habito Cigarrillo"
                        color-line="red"
                        ref="cigaretteHabit"/>
                </b-card>
            </b-col>
        </b-row>

        <div class="row float-right pt-10 pr-10">
            <template>
                <b-btn variant="default" :to="{name: 'biologicalmonitoring-musculoskeletalanalysis'}">Atras</b-btn>
            </template>
        </div>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import ChartPie from '@/components/ECharts/ChartPie.vue';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
    name: 'biologicalmonitoring-musculoskeletalAnalysis-informs',
    metaInfo: {
        title: 'Análisis Osteomuscular - Informes'
    },
    components:{
        ChartPie,
        FilterGeneral
    },
    data () {
        return {
            filters: [],
            isLoading: false,

            cardiovascularRisk: {
                labels: [],
                datasets: []
            },
            osteomuscularGroup: {
                labels: [],
                datasets: []
            },
            imcClassification: {
                labels: [],
                datasets: []
            },
            abdominalPerimeterClassification: {
                labels: [],
                datasets: []
            },
            consolidatedRiskCriterion: {
                labels: [],
                datasets: []
            },
            prioritizationMedicalCriteria: {
                labels: [],
                datasets: []
            },
            exerciseHabit: {
                labels: [],
                datasets: []
            },
            liquorHabit: {
                labels: [],
                datasets: []
            },
            cigaretteHabit: {
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

                axios.post('/biologicalmonitoring/musculoskeletalAnalysis/informs', this.filters)
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