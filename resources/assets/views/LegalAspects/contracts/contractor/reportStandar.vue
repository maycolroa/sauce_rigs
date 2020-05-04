<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="REPORTE DE ESTANDARES MÍNIMOS"
        url="legalaspects-contracts"
    />

    <!--<div>
        <filter-general 
            v-model="filters" 
            configName="" 
            :isDisabled="isLoading"/>
    </div>-->

    <div class="col-md">
        <b-card border-variant="primary" title="Estandares mínimos" class="mb-3 box-shadow-none">
            <b-row>
                <b-col class="text-center" style="padding-bottom: 15px;">
                    <h4>Rango de Cumplimiento por Contratista</h4>
                </b-col>
            </b-row>
            <b-row>
                <div class="col-md-12">
                    <chart-bar 
                        :chart-data="contracts"
                        title="Rango de Cumplimiento por Contratista"
                        color-line="red"
                        ref=""/>
                </div>
            </b-row>
            <b-row>
                <b-col class="text-center" style="padding-bottom: 15px;">
                    <h4>Porcentaje de Cumplimiento por estandar</h4>
                </b-col>
            </b-row>
            <b-row>
                <div class="col-md-12">
                    <chart-bar-compliance 
                        :chart-data="standar"
                        title="Porcentaje de Cumplimiento por estandar"
                        color-line="red"
                        ref=""/>
                </div>
            </b-row>
        </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import ChartBarCompliance from '@/components/ECharts/ChartBarCompliance.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
    name: 'legalaspects-evaluations-report',
    metaInfo: {
        title: 'Estandares mínimos - Reportes'
    },
    components:{
        ChartBarCompliance,
        ChartBar,
        FilterGeneral
    },
    data () {
        return {
            filters: [],
            isLoading: false,
            contracts: {
                labels: [],
                datasets: []
            },
            standar: {
                labels: [],
                datasets: []
            }
        }
    },
    created(){
        this.fetch()
    },
    /*watch: {
        filters: {
            handler(val){
                this.fetch()
            },
            deep: true
        }
    },*/
    methods: {
        /*setFilters(value)
        { 
            this.filters = value
            this.updateTotales()
            this.fetch()
        },*/
        fetch()
        {
            if (!this.isLoading)
            {
                //console.log('buscando...')
                this.isLoading = true;

                axios.post('/legalAspects/listCheck/report'/*, this.filters*/)
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