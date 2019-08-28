<template>
    <div>
        <h4 class="font-weight-bold mb-4">
            <span class="text-muted font-weight-light">Análisis Osteomuscular /</span> Informes
        </h4>
        <div>
            <filter-general 
                v-model="filters" 
                configName="biologicalmonitoring-musculoskeletalAnalysis" 
                :isDisabled="isLoading"/>
        </div>

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

            consolidatedRiskCriterion: {
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