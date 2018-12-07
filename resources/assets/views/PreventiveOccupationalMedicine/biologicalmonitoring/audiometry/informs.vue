<template>
    <div>
        <h4 class="font-weight-bold mb-4">
            <span class="text-muted font-weight-light">Audiometrias /</span> Informes
        </h4>
        
        <div class="row" style="padding-bottom: 10px;">
            <div class="col-md">
            <b-card no-body>
                <b-card-body>
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="selectedRegionals" :multiple="true" :options="regionals" :searchable="true" name="regionals" label="Regionales">
                            </vue-advanced-select></b-col>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="selectedHeadquarters" :multiple="true" :options="headquarters" :searchable="true" name="headquarters" label="Sedes">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="selectedAreas" :multiple="true" :options="areas" :searchable="true" name="areas" label="Áreas">
                            </vue-advanced-select></b-col>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="selectedProcesses" :multiple="true" :options="processes" :searchable="true" name="processes" label="Procesos">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="selectedBusinesses" :multiple="true" :options="businesses" :searchable="true" name="businesses" label="Centros de costo">
                            </vue-advanced-select></b-col>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="selectedPositions" :multiple="true" :options="positions" :searchable="true" name="positions" label="Cargos">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="selectedYears" :multiple="true" :options="years" :searchable="true" name="years" label="Años">
                            </vue-advanced-select></b-col>
                    </b-row>
                </b-card-body>
            </b-card>
            </div>
        </div>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Derecha Aéreo PTA" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="airRightPtaPie"
                        title="Informes PTA"
                        color-line="red"
                        ref="airRightPtaPie"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Izquierda Aéreo PTA" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="airLeftPtaPie"
                        title="Informes PTA"
                        color-line="blue"
                        ref="airLeftPtaPie"/>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Población Expuesta" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="exposedPopulationSelected" :options="selectBar" :searchable="true" name="exposedPopulationSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="4">
                            <b>Total Expuestos {{ exposedPopulationData.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <chart-bar 
                        :chart-data="exposedPopulationData"
                        title="Población Expuesta"
                        ref="exposedPopulation"/>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Población Expuesta con CUAT" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="exposedPopulationCuatSelected" :options="selectBar" :searchable="true" name="exposedPopulationCuatSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="6">
                            <b>Total Expuestos con CUAT {{ exposedPopulationCuatData.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <chart-bar 
                        :chart-data="exposedPopulationCuatData"
                        title="Población Expuesta con CUAT"
                        ref="exposedPopulationCuat"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Población Expuesta con CUAP" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="exposedPopulationCuapSelected" :options="selectBar" :searchable="true" name="exposedPopulationCuapSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="6">
                            <b>Total Expuestos con CUAP {{ exposedPopulationCuapData.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <chart-bar 
                        :chart-data="exposedPopulationCuapData"
                        title="Población Expuesta con CUAP"
                        ref="exposedPopulationCuap"/>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="DX Audiometrias" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="exposedPopulationaudiologicalConditionSelected" :options="selectBar" :searchable="true" name="exposedPopulationaudiologicalConditionSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="4">
                            <b>Total Normal {{ exposedPopulationaudiologicalConditionData.datasets.count.Normal }} </b>
                            <br>
                            <b>Total Alterada {{ exposedPopulationaudiologicalConditionData.datasets.count.Alterada }} </b>
                        </b-col>
                    </b-row>
                    <chart-bar-multiple
                        :chart-data="exposedPopulationaudiologicalConditionData"
                        title="DX Audiometrias"
                        ref="exposedPopulationaudiologicalCondition"/>
                </b-card>
            </b-col>
        </b-row>

        <b-btn variant="default" :to="{name: 'biologicalmonitoring-audiometry'}">Atras</b-btn>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import ChartPie from '@/components/ECharts/ChartPie.vue';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import ChartBarMultiple from '@/components/ECharts/ChartBarMultiple.vue';

export default {
    name: 'audiometry-report-pta',
    metaInfo: {
        title: 'Audiometria - Informes'
    },
    components:{
        VueAdvancedSelect,
        ChartPie,
        ChartBar,
        ChartBarMultiple
    },
    data () {
        return {
            regionals: [],
            selectedRegionals: [],
            headquarters: [],
            selectedHeadquarters: [],
            areas: [],
            selectedAreas: [],
            processes: [],
            selectedProcesses: [],
            businesses: [],
            selectedBusinesses: [],
            positions: [],
            selectedPositions: [],
            years: [],
            selectedYears: [],
            selectBar: [],

            updateTimeout: 0,
            ready: {
                regionals: false,
                headquarters: false,
                areas: false,
                processes: false,
                businesses: false,
                positions: false,
                years: false
            },
            isLoading: false,

            airLeftPtaPie: {
                labels: [],
                datasets: []
            },
            airRightPtaPie: {
                labels: [],
                datasets: []
            },
            exposedPopulation: {
                employee_regional_id: {
                    labels: [],
                    datasets: []
                },
                employee_headquarter_id: {
                    labels: [],
                    datasets: []
                },
                employee_area_id: {
                    labels: [],
                    datasets: []
                },
                employee_process_id: {
                    labels: [],
                    datasets: []
                },
                employee_business_id: {
                    labels: [],
                    datasets: []
                },
                employee_position_id: {
                    labels: [],
                    datasets: []
                }
            },
            exposedPopulationSelected: 'employee_regional_id',
            exposedPopulationCuat: {
                employee_regional_id: {
                    labels: [],
                    datasets: []
                },
                employee_headquarter_id: {
                    labels: [],
                    datasets: []
                },
                employee_area_id: {
                    labels: [],
                    datasets: []
                },
                employee_process_id: {
                    labels: [],
                    datasets: []
                },
                employee_business_id: {
                    labels: [],
                    datasets: []
                },
                employee_position_id: {
                    labels: [],
                    datasets: []
                }
            },
            exposedPopulationCuatSelected: 'employee_regional_id',
            exposedPopulationCuap: {
                employee_regional_id: {
                    labels: [],
                    datasets: []
                },
                employee_headquarter_id: {
                    labels: [],
                    datasets: []
                },
                employee_area_id: {
                    labels: [],
                    datasets: []
                },
                employee_process_id: {
                    labels: [],
                    datasets: []
                },
                employee_business_id: {
                    labels: [],
                    datasets: []
                },
                employee_position_id: {
                    labels: [],
                    datasets: []
                }
            },
            exposedPopulationCuapSelected: 'employee_regional_id',
            exposedPopulationaudiologicalCondition: {
                employee_regional_id: {
                    labels: [],
                    datasets: {
                        count: []
                    }
                },
                employee_headquarter_id: {
                    labels: [],
                    datasets: {
                        count: []
                    }
                },
                employee_area_id: {
                    labels: [],
                    datasets: {
                        count: []
                    }
                },
                employee_process_id: {
                    labels: [],
                    datasets: {
                        count: []
                    }
                },
                employee_business_id: {
                    labels: [],
                    datasets: {
                        count: []
                    }
                },
                employee_position_id: {
                    labels: [],
                    datasets: {
                        count: []
                    }
                }
            },
            exposedPopulationaudiologicalConditionSelected: 'employee_regional_id',
        }
    },
    created(){
        this.fetchSelect('selectBar', '/selects/multiselectBar')
        this.fetchSelect('regionals', '/selects/regionals')
        this.fetchSelect('headquarters', '/selects/headquarters')
        this.fetchSelect('areas', '/selects/areas')
        this.fetchSelect('processes', '/selects/processes')
        this.fetchSelect('businesses', '/selects/businesses')
        this.fetchSelect('positions', '/selects/positions')
        this.fetchSelect('years', '/selects/years/audiometry')
    },
    watch: {
        regionals() {
            this.selectedRegionals = this.regionals
            this.ready.regionals = true
        },
        headquarters() {
            this.selectedHeadquarters = this.headquarters
            this.ready.headquarters = true
        },
        areas() {
            this.selectedAreas = this.areas
            this.ready.areas = true
        },
        processes() {
            this.selectedProcesses = this.processes
            this.ready.processes = true
        },
        businesses() {
            this.selectedBusinesses = this.businesses
            this.ready.businesses = true
        },
        positions() {
            this.selectedPositions = this.positions
            this.ready.positions = true
        },
        years() {
            this.selectedYears = this.years
            this.ready.years = true
        },
        selectedRegionals() {
            this.fetch()
        },
        selectedHeadquarters() {
            this.fetch()
        },
        selectedAreas() {
            this.fetch()
        },
        selectedProcesses() {
            this.fetch()
        },
        selectedBusinesses() {
            this.fetch()
        },
        selectedPositions() {
            this.fetch()
        },
        selectedYears() {
            this.fetch()
        }
    },
    computed: {
        exposedPopulationData: function() {
            return this.exposedPopulation[this.exposedPopulationSelected]
        },
        exposedPopulationCuatData: function() {
            return this.exposedPopulationCuat[this.exposedPopulationCuatSelected]
        },
        exposedPopulationCuapData: function() {
            return this.exposedPopulationCuap[this.exposedPopulationCuapSelected]
        },
        exposedPopulationaudiologicalConditionData: function() {
            return this.exposedPopulationaudiologicalCondition[this.exposedPopulationaudiologicalConditionSelected]
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
        isReady()
        {
            if (this.ready.regionals && this.ready.headquarters && this.ready.areas && this.ready.processes && this.ready.businesses && this.ready.positions && this.ready.years)
            {
                return true
            }
            else 
                return false
        },
        fetch()
        {
            if (this.isReady() && !this.isLoading)
            {
                console.log('buscando...')
                this.isLoading = true;

                axios.post('/biologicalmonitoring/audiometry/informs', {
                    regionals: this.selectedRegionals,
                    headquarters: this.selectedHeadquarters,
                    areas: this.selectedAreas,
                    processes: this.selectedProcesses,
                    businesses: this.selectedBusinesses,
                    positions: this.selectedPositions,
                    years: this.selectedYears
                })
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