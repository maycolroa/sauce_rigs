<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="REPORTE DE ESTANDARES MÍNIMOS"
        url="legalaspects-contracts"
    />

    <div>
        <filter-general 
            v-model="filters" 
            configName="legalaspects-contracts-list-check-report" 
            :isDisabled="isLoading"/>
    </div>

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
        <b-card border-variant="primary" title="Documentos cargados por empleados" class="mb-3 box-shadow-none">
             <vue-table
                configName="legalaspects-contract-documents-employee-report"
                :params="{filters}"
                ref="documentEmployee"
                ></vue-table>
        </b-card>
        <b-card v-if="exists" border-variant="primary" title="Documentos globales" class="mb-3 box-shadow-none">
             <vue-table
                configName="legalaspects-contract-documents-global-report"
                :params="{filters}"
                ref="documentGlobal"
                ></vue-table>
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
            exists: true,
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
    watch: {
        filters: {
            handler(val){
                this.$refs.documentEmployee.refresh()
                this.$refs.documentGlobal.refresh()
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

                axios.post('/legalAspects/listCheck/report', this.filters)
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