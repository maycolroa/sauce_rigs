<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="CONSULTA DOCUMENTOS"
        url="legalaspects-contracts"
    />

    <!--<div>
        <filter-general 
            v-model="filters" 
            configName="legalaspects-contracts-list-check-report" 
            :isDisabled="isLoading"/>
    </div>-->

    <div class="col-md">
        <b-tabs card pills class="nav-responsive-md md-pills-light">
            <b-tab>
                <template slot="title">
                    <strong>Documentos de empleados pendientes por cargar</strong> 
                </template>
                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-consulting-employee-report"
                        :params="{filters}"
                        :customColumnsName="true" 
                        ref="documentEmployee"
                    ></vue-table>
                </b-card>
            </b-tab>
            <b-tab>
                <template slot="title">
                    <strong>Documentos de empleados pendientes por vencimiento</strong> 
                </template>
                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-consulting-employee-report-expired"
                        :params="{filters}"
                        :customColumnsName="true" 
                        ref="documentEmployeeExpired"
                    ></vue-table>
                </b-card>
            </b-tab>

            <!--<b-tab>
                <template slot="title">
                    <strong>Documentos de contratistas pendientes por cargar</strong> 
                </template>
                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-consulting-contract-report"
                        :params="{filters}"
                        :customColumnsName="true" 
                        ref="documentContract"
                    ></vue-table>
                </b-card>
            </b-tab>-->
                <!--<b-card border-variant="primary" title="Documentos de empleados pendientes por vencimiento" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-global-report"
                        :params="{filters}"
                        ref="documentGlobal"
                        ></vue-table>
                </b-card>
                <b-card border-variant="primary" title="Documentos de empleados proximos a vencerse" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-trainig-employee-report-consolidated"
                        :params="{filters}"
                        ref="consolidated"
                        ></vue-table>
                </b-card>
                <b-card border-variant="primary" title="Documentos de contratistas pendientes por cargar" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-trainig-employee-report-details"
                        :params="{filters}"
                        ref="details"
                        ></vue-table>
                </b-card>
                <b-card border-variant="primary" title="Documentos de contratistas proximos a vencerse" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-trainig-employee-report-details"
                        :params="{filters}"
                        ref="details"
                        ></vue-table>
                </b-card>
            </b-tab>-->
        </b-tabs>
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
        title: 'Consulta documentos'
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
                this.$refs.consolidated.refresh()                
                this.$refs.details.refresh()
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