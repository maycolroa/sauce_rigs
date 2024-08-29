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
                        :customColumnsName="true" 
                        ref="documentEmployeeExpired"
                    ></vue-table>
                </b-card>
            </b-tab>
            
            <b-tab>
                <template slot="title">
                    <strong>Documentos de empleados proximos a vencerse</strong> 
                </template>
                <b-row>
                    <b-col>
                        <vue-input class="col-md-3" v-model="days" label="Dias" type="number" pattern="[0-9]*" name="days" placeholder="Dias"></vue-input>
                    </b-col>
                </b-row>

                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-consulting-employee-report-close-winning"
                        :params="{days}"
                        :customColumnsName="true" 
                        ref="documentEmployeeCloseToWinning"
                    ></vue-table>
                </b-card>
            </b-tab>

            <b-tab>
                <template slot="title">
                    <strong>Documentos de contratistas pendientes por cargar</strong> 
                </template>
                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-consulting-contract-report"
                        :customColumnsName="true" 
                        ref="documentContract"
                    ></vue-table>
                </b-card>
            </b-tab>

            <b-tab>
                <template slot="title">
                    <strong>Documentos de contratistas pendientes por vencimiento</strong> 
                </template>
                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-consulting-contract-report-expired"
                        :customColumnsName="true" 
                        ref="documentContractExpired"
                    ></vue-table>
                </b-card>
            </b-tab>

            <b-tab>
                <template slot="title">
                    <strong>Documentos de contratistas proximos a vencerse</strong> 
                </template>
                <b-row>
                    <b-col>
                        <vue-input class="col-md-3" v-model="daysContract" label="Dias" type="number" pattern="[0-9]*" name="daysContract" placeholder="Dias"></vue-input>
                    </b-col>
                </b-row>

                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <vue-table
                        configName="legalaspects-contract-documents-consulting-contract-report-close-winning"
                        :params="{daysContract}"
                        :customColumnsName="true" 
                        ref="documentContractCloseToWinning"
                    ></vue-table>
                </b-card>
            </b-tab>
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
import VueInput from "@/components/Inputs/VueInput.vue";

export default {
    name: 'legalaspects-evaluations-report',
    metaInfo: {
        title: 'Consulta documentos'
    },
    components:{
        ChartBarCompliance,
        ChartBar,
        FilterGeneral,
        VueInput
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
            },
            days: 15,
            daysContract: 15
        }
    },
    watch: {
        days: {
            handler(val){
                this.$refs.documentEmployeeCloseToWinning.refresh()
            },
            deep: true
        },
        daysContract: {
            handler(val){
                this.$refs.documentContractCloseToWinning.refresh()
            },
            deep: true
        }
    }
}
</script>