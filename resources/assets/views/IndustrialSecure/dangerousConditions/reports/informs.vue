<template>
    <div>
        <header-module
            title="INSPECCIONES"
            subtitle="REPORTE INSPECCIONES NO PLANEADAS"
            url="dangerousconditions-reports"
        />

        <div>
            <filter-general 
                v-model="filters" 
                configName="dangerousconditions-report-informs" 
                :isDisabled="isLoading"/>
        </div>

        <b-row style="padding-top: 15px;">
            <b-col>
                <b-card border-variant="primary" title="Inspecciones no Planeadas" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="reportSelected" :options="selectBar" :searchable="true" name="reportSelected" :allowEmpty="false" :hideSelected="false">
                            </vue-advanced-select></b-col>
                         <b-col><vue-advanced-select :disabled="isLoading" v-model="years" :options="yearsOptions" :searchable="true" name="years" placeholder="Años" :multiple="true">
                        </vue-advanced-select></b-col>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="months" :options="monthsOptions" :searchable="true" name="months" placeholder="Meses" :multiple="true">
                        </vue-advanced-select></b-col>
                    </b-row>
                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Número de Inspecciones no planeadas</h4>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-12">
                            <chart-bar 
                                :chart-data="reportData"
                                title="Número de inspecciones no planeadas realizadas"
                                color-line="red"
                                ref=""/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
       <!--<b-row>
            <b-col>
                <b-card border-variant="primary" :title="`${keywordCheck('headquarters')} con más reportes`" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="report_per_headquarter"
                        :title="`${keywordCheck('headquarters')} con más reportes`"
                        color-line="red"
                        ref="report_per_headquarter"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" :title="`${keywordCheck('areas')} con más reportes`" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="report_per_area"
                        :title="`${keywordCheck('areas')} con más reportes`"
                        color-line="red"
                        ref="report_per_area"/>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card border-variant="primary" :title="`${keywordCheck('headquarters')} y ${keywordCheck('areas')} con más reportes`" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="report_per_headquarter_area"
                        :title="`${keywordCheck('headquarters')} y ${keywordCheck('areas')} con más reportes`"
                        color-line="red"
                        ref="report_per_area"/>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Usuarios con más reportes" class="mb-3 box-shadow-none">
                    <chart-bar 
                        :chart-data="report_per_user"
                        title="Usuarios con más reportes"
                        color-line="red"
                        ref="report_per_user"/>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <vue-advanced-select @input="conditionHeadquarter" class="col-md-12" v-model="headquarter_selected" :multiple="false" :options="options_headquarter" :hide-selected="false" name="headquarter_selected"  :label="`Condición más reportada por ${keywordCheck('headquarter')}`" placeholder="Seleccione una opción">
            </vue-advanced-select>
        </b-row>

        <b-row>
            <div style="padding-left: 15px;"><b>Condición:</b> {{ result.condition }}</div>
        </b-row>

        <b-row>
            <div style="padding-left: 15px;"><b>Número de reportes:</b> {{ result.reports }}</div>
        </b-row>-->

        <div class="row float-right pt-10 pr-10" style="padding-top: 20px; padding-right: 20px;">
            <template>
                <b-btn variant="default" :to="{name: 'dangerousconditions-reports'}" :disabled="isLoading">Atras</b-btn>
            </template>
        </div>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import GlobalMethods from '@/utils/GlobalMethods.js';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
    name: 'reports-informs',
    metaInfo: {
        title: 'Reportes - Inpsecciones no planeadas'
    },
    components:{
        ChartBar,
        VueAdvancedSelect,
        GlobalMethods,
        FilterGeneral
    },
    data () {
        return {
            filters: [],
            isLoading: false,
            yearsOptions: [],
            monthsOptions: [],
            report_per_headquarter: {
                labels: [],
                datasets: []
            },
            report_per_area: {
                labels: [],
                datasets: []
            },
            report_per_user: {
                labels: [],
                datasets: []
            },
            report_per_headquarter_area: {
                labels: [],
                datasets: []
            },
            options_headquarter: [],
            headquarter_selected: '',
            result: {
                reports: 0, 
                condition: ''
            },
            selectBar: [],
            reportSelected: 'rate',
            years: '',
            months: '',
            reports: {
                user: {
                    labels: [],
                    datasets: []
                },
                rate: {
                    labels: [],
                    datasets: []
                },
                condition: {
                    labels: [],
                    datasets: []
                },
                headquarter: {
                    labels: [],
                    datasets: []
                },
                area: {
                    labels: [],
                    datasets: []
                },
                regional: {
                    labels: [],
                    datasets: []
                },
                process: {
                    labels: [],
                    datasets: []
                }
            },

        }
    },
    created(){
        this.fetchSelect('selectBar', '/selects/multiselectBarReports')
        this.fetchSelect('yearsOptions', '/selects/reportDinamic/years')
        this.fetchSelect('monthsOptions', '/selects/reportDinamic/months')
        this.fetch()
    },
    computed: {
        reportData: function() {
            return this.reports[this.reportSelected]
        }
    },
    watch: {
        years() {
          this.fetch();
        },
        months() {
          this.fetch();
        },
        filters: {
            handler(val){
                this.fetch()
            },
            deep: true
        }
    },
    methods: {
        fetchSelect(key, url)
        {
            GlobalMethods.getDataMultiselect(url)
            .then(response => {
                this[key] = response;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                this.$router.go(-1);
            });
        },
        conditionHeadquarter()
        {
            if (!this.isLoading)
            {
                this.isLoading = true;

                axios.post('/industrialSecurity/dangerousConditions/report/conditionHeadquarter', {id: this.headquarter_selected})
                .then(data => {
                    this.result = data.data;
                    this.isLoading = false;
                })
                .catch(error => {
                    console.log(error);
                    this.isLoading = false;
                    Alerts.error('Error', 'Hubo un problema recolectando la información');
                });
            }
        },
        fetch()
        {
            if (!this.isLoading)
            {
                //console.log('buscando...')
                this.isLoading = true;

                let postData = Object.assign({}, {years: this.years}, {months: this.months}, this.filters);

                axios.post('/industrialSecurity/dangerousConditions/report/informs', postData)
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