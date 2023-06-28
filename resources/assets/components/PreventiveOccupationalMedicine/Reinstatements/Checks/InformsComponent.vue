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

                        <b-row v-if="form != 'argos'">
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
                        <b-row v-if="form != 'argos'">
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
                                <b-card border-variant="primary" :title="`Reportes por ${keywordCheck('disease_origin')}`"  class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ disease_origin_reports_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col v-if="form != 'argos'">
                                            <table-report
                                                :rows="disease_origin_reports_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="disease_origin_reports_pie.chart"
                                                :title="keywordCheck('disease_origin')"
                                                ref="disease_origin_reports_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por motivo de cierre"  class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ closed_motive_reports_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="closed_motive_reports_pie.chart"
                                                title="Reportes por motivo de cierre"
                                                ref="closed_motive_reports_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" :title="`Reportes por ${keywordCheck('regional')}`" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_regional_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col v-if="form != 'argos'">
                                            <table-report
                                                :rows="cases_per_regional_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_regional_pie.chart"
                                                :title="`Reportes por ${keywordCheck('regional')}`"
                                                ref="cases_per_regional_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row v-if="locationForm.headquarter == 'SI' && form != 'argos'">
                            <b-col>
                                <b-card border-variant="primary" :title="`Reportes por ${keywordCheck('headquarter')}`" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_headquarter_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col>
                                            <table-report
                                                :rows="cases_per_headquarter_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_headquarter_pie.chart"
                                                :title="`Reportes por ${keywordCheck('headquarter')}`"
                                                ref="cases_per_headquarter_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row v-if="locationForm.process == 'SI' && form != 'argos'">
                            <b-col>
                                <b-card border-variant="primary" :title="`Reportes por ${keywordCheck('process')}`" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_process_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col>
                                            <table-report
                                                :rows="cases_per_process_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_process_pie.chart"
                                                :title="`Reportes por ${keywordCheck('process')}`"
                                                ref="cases_per_process_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" :title="`Reportes por ${keywordCheck('businesses')}`" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_business_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col v-if="form != 'argos'">
                                            <table-report
                                                :rows="cases_per_business_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_business_pie.chart"
                                                :title="`Reportes por ${keywordCheck('businesses')}`"
                                                color-line="red"
                                                ref="cases_per_business_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-card border-variant="primary" :title="`Reporte de ${keywordCheck('employees')} en el programa de reincorporaciones`" class="mb-3 box-shadow-none">
                                    
                                    <b-row>
                                        <b-col>
                                            <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; margin-bottom: 0px">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="text-center align-middle">Descripción</th>
                                                        <th scope="col" class="text-center align-middle">Activos</th>
                                                        <th scope="col" class="text-center align-middle">Inactivos</th>
                                                        <th scope="col" class="text-center align-middle">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-for="(record, index3) in employee_active_disease_origin.table">
                                                    <tr :key="index3">
                                                        <td class="align-middle">{{record[1].disease_origin}}</td>
                                                        <td class="align-middle">{{record[1].activo}}</td>
                                                        <td class="align-middle">{{record[1].inactivo}}</td>
                                                        <td class="align-middle">{{record[1].total}}</td>
                                                    </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row v-if="form == 'argos'">
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por categoría Código CIE 10" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_cie_10_pie.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_cie_10_pie"
                                                title="Código CIE 10"
                                                ref="cases_per_cie_10_pie"/>
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
                                            <b>Total reportes: {{ cases_per_sve_associateds_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col>
                                            <table-report
                                                :rows="cases_per_sve_associateds_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_sve_associateds_pie.chart"
                                                title="Reportes por SVE Asociados"
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
                                            <b>Total reportes: {{ cases_per_medical_certificates_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col>
                                            <table-report
                                                :rows="cases_per_medical_certificates_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_medical_certificates_pie.chart"
                                                title="Reportes por Certificado médico UEAC"
                                                ref="cases_per_medical_certificates_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row v-if="form == 'vivaAir'">
                            <b-col>
                                <b-card border-variant="primary" title="Reportes por Tipos de Reintegro" class="mb-3 box-shadow-none">
                                    <b-row align-h="end">
                                        <b-col cols="2">
                                            <b>Total reportes: {{ cases_per_relocated_types_pie.chart.datasets.count }} </b>
                                        </b-col>
                                    </b-row>
                                    <b-row>
                                        <!--<b-col>
                                            <table-report
                                                :rows="cases_per_relocated_types_pie.table"
                                            />
                                        </b-col>-->
                                        <b-col>
                                            <chart-bar 
                                                :chart-data="cases_per_relocated_types_pie.chart"
                                                title="Reportes por Tipos de Reintegro"
                                                ref="cases_per_relocated_types_pie"/>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                    </b-tab>
                    <b-tab v-if="form != 'argos'">
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
                                        <chart-bar 
                                            :chart-data="cases_per_cie_10_per_EG_pie"
                                            title="Código CIE 10 por EG"
                                            color-line="red"
                                            ref="cases_per_cie_10_per_EG_pie"/>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </div>
                    </b-tab>
                    <b-tab v-if="form != 'argos'">
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
                                        <chart-bar 
                                            :chart-data="cases_per_cie_10_per_EL_pie"
                                            title="Código CIE 10 por EL"
                                            color-line="red"
                                            ref="cases_per_cie_10_per_EL_pie"/>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </div>
                    </b-tab>
                    <b-tab v-if="form != 'argos'">
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
                                        <chart-bar 
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
import TableReport from './TableReport.vue';

export default {
    name: 'reinstatements-informs',
    props: {
        form: { type: String, default: '' }
    },
    components:{
        Headers,
        TableReport,
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
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
            },
            cases_per_regional_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
            },
            cases_per_headquarter_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
            },
            cases_per_relocated_types_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
            },
            cases_per_process_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
            },
            cases_per_business_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
            },
            employee_active_disease_origin: {
                table: [],
            },
            cases_per_sve_associateds_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
            },
            cases_per_medical_certificates_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
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
            },
            cases_per_cie_10_pie: {
                labels: [],
                datasets: []
            },
            closed_motive_reports_pie: {
                table: [],
                chart: {
                    labels: [],
                    datasets: []
                }
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
                    console.log(this[key])
                    console.log(value)
                    this[key] = value;
                }
            });
        }
    }
}
</script>