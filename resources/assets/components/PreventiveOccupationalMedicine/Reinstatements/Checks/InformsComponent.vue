<template>
    <div>
        <div>
            <filter-reinc-checks 
                  v-model="filters" 
                  configName="reinstatements-checks" 
                  :isDisabled="isLoading"/>
        </div>

        <b-form-row>
            <b-card no-body variant="white" class="mb-3" style="width: 100%;">
                <b-tabs card pills class="nav-responsive-md md-pills-light">
                    <b-tab>
                        <template slot="title">
                            <strong>General</strong> 
                        </template>

                        <b-row>
                            <b-col>
                                <headers
                                    :data="headers"
                                    ref="headers"
                                />
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes abiertos por año de creación" class="mb-3 box-shadow-none">
                                    <chart-bar 
                                        :chart-data="open_reports_bar_year"
                                        title="Reportes abiertos por año de creación"
                                        ref="open_reports_bar_year"/>
                                </b-card>
                            </b-col>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes cerrados por año de creación" class="mb-3 box-shadow-none">
                                    <chart-bar 
                                        :chart-data="closed_reports_bar_year"
                                        title="Reportes cerrados por año de creación"
                                        ref="closed_reports_bar_year"/>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes abiertos por mes de creación" class="mb-3 box-shadow-none">
                                    <chart-bar 
                                        :chart-data="open_reports_bar_month"
                                        title="Reportes abiertos por mes de creación"
                                        ref="open_reports_bar_month"/>
                                </b-card>
                            </b-col>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes cerrados por mes de creación" class="mb-3 box-shadow-none">
                                    <chart-bar 
                                        :chart-data="closed_reports_bar_month"
                                        title="Reportes cerrados por mes de creación"
                                        ref="closed_reports_bar_month"/>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por origen de enfermedad" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ disease_origin_reports_pie.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-pie 
                                                :chart-data="disease_origin_reports_pie"
                                                title="Origen de enfermedad"
                                                color-line="red"
                                                ref="disease_origin_reports_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por Regional" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_regional_pie.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-pie 
                                                :chart-data="cases_per_regional_pie"
                                                title="Reportes por Regional"
                                                color-line="red"
                                                ref="cases_per_regional_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por Sedes" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_headquarter_pie.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-pie 
                                                :chart-data="cases_per_headquarter_pie"
                                                title="Reportes por Sedes"
                                                color-line="red"
                                                ref="cases_per_headquarter_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por Centro de Costos" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_business_pie.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-pie 
                                                :chart-data="cases_per_business_pie"
                                                title="Reportes por Centro de Costos"
                                                color-line="red"
                                                ref="cases_per_business_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row v-if="form == 'vivaAir'">
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por SVE Asociados" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_sve_associateds_pie.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-pie 
                                                :chart-data="cases_per_sve_associateds_pie"
                                                title="Reportes por SVE Asociados"
                                                color-line="red"
                                                ref="cases_per_sve_associateds_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row v-if="form == 'vivaAir'">
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por Certificado médico UEAC" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_medical_certificates_pie.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-pie 
                                                :chart-data="cases_per_medical_certificates_pie"
                                                title="Reportes por Certificado médico UEAC"
                                                color-line="red"
                                                ref="cases_per_medical_certificates_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                    </b-tab>
                    <b-tab>
                        <template slot="title">
                        <strong>Reportes por categoría Código CIE 10 por EG</strong> 
                        </template>
                        <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
                            <b-card border-variant="primary" title="Reportes por categoría Código CIE 10 por EG" class="mb-3 box-shadow-none">
                                <b-row align-h="end">
                                    <b-col cols="2">
                                        <b>Total reportes: {{ cases_per_cie_10_per_EG_pie.datasets.count }} </b>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col>
                                        <chart-pie 
                                            :chart-data="cases_per_cie_10_per_EG_pie"
                                            title="Código CIE 10 por EG"
                                            color-line="red"
                                            ref="cases_per_cie_10_per_EG_pie"/>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </div>
                    </b-tab>
                    <b-tab>
                        <template slot="title">
                            <strong>Reportes por categoría Código CIE 10 por EL</strong> 
                        </template>
                        <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
                            <b-card border-variant="primary" title="Reportes por categoría Código CIE 10 por EL" class="mb-3 box-shadow-none">
                                <b-row align-h="end">
                                    <b-col cols="2">
                                        <b>Total reportes: {{ cases_per_cie_10_per_EL_pie.datasets.count }} </b>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col>
                                        <chart-pie 
                                            :chart-data="cases_per_cie_10_per_EL_pie"
                                            title="Código CIE 10 por EL"
                                            color-line="red"
                                            ref="cases_per_cie_10_per_EL_pie"/>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </div>
                    </b-tab>
                    <b-tab>
                        <template slot="title">
                            <strong>Reportes por categoría Código CIE 10 por AT</strong> 
                        </template>
                        <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
                            <b-card border-variant="primary" title="Reportes por categoría Código CIE 10 por AT" class="mb-3 box-shadow-none">
                                <b-row align-h="end">
                                    <b-col cols="2">
                                        <b>Total reportes: {{ cases_per_cie_10_per_AT_pie.datasets.count }} </b>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col>
                                        <chart-pie 
                                            :chart-data="cases_per_cie_10_per_AT_pie"
                                            title="Código CIE 10 por AT"
                                            color-line="red"
                                            ref="cases_per_cie_10_per_AT_pie"/>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </div>
                    </b-tab>
                </b-tabs>
            </b-card>
        </b-form-row>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import ChartPie from '@/components/ECharts/ChartPie.vue';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import FilterReincChecks from '@/components/Filters/FilterReincChecks.vue';
import Headers from './Headers.vue';

export default {
    name: 'reinstatements-informs',
    props: {
        form: { type: String, default: '' }
    },
    components:{
        Headers,
        ChartPie,
        ChartBar,
        FilterReincChecks
    },
    data () {
        return {
            filters: [],
            isLoading: false,

            headers: {},
            open_reports_bar_year: {
                labels: [],
                datasets: []
            },
            open_reports_bar_month: {
                labels: [],
                datasets: []
            },
            closed_reports_bar_year: {
                labels: [],
                datasets: []
            },
            closed_reports_bar_month: {
                labels: [],
                datasets: []
            },
            disease_origin_reports_pie: {
                labels: [],
                datasets: []
            },
            cases_per_regional_pie: {
                labels: [],
                datasets: []
            },
            cases_per_headquarter_pie: {
                labels: [],
                datasets: []
            },
            cases_per_business_pie: {
                labels: [],
                datasets: []
            },
            cases_per_sve_associateds_pie: {
                labels: [],
                datasets: []
            },
            cases_per_medical_certificates_pie: {
                labels: [],
                datasets: []
            },
            cases_per_cie_10_per_EG_pie: {
                labels: [],
                datasets: []
            },
            cases_per_cie_10_per_EL_pie: {
                labels: [],
                datasets: []
            },
            cases_per_cie_10_per_AT_pie: {
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

                axios.post('/biologicalmonitoring/reinstatements/check/informs', this.filters)
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