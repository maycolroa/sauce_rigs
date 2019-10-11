<template>
    <div>
        <header-module
            title="AUDIOMETRIAS"
            subtitle="INFORMES"
            url="biologicalmonitoring-audiometry"
        />
        <div>
            <filter-general 
                v-model="filters" 
                configName="biologicalmonitoring-audiometry-informs" 
                :isDisabled="isLoading"/>
        </div>

        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Estado Audición Derecho" class="mb-3 box-shadow-none">
                    <chart-pie 
                        :chart-data="airRightPtaPie"
                        title="Informes PTA"
                        color-line="red"
                        ref="airRightPtaPie"/>
                </b-card>
            </b-col>
            <b-col>
                <b-card border-variant="primary" title="Estado Audición Izquierdo" class="mb-3 box-shadow-none">
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
                <b-card border-variant="primary" title="Población Expuesta con CUAT" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="exposedPopulationCuatSelected" :options="selectBar" :searchable="true" name="exposedPopulationCuatSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="6">
                            <b>Total Expuestos con CUAT {{ exposedPopulationCuatData.percentage_x_category.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-6" style="padding-bottom: 15px;">
                            <div class="table-responsive" v-if="exposedPopulationCuatData.total.length > 0">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">Descripción</th>
                                            <th class="text-center align-middle">Total</th>
                                            <th class="text-center align-middle">% Respecto a la población</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, index) in exposedPopulationCuatData.total" :key="`cuat-${index}`">
                                            <td v-for="(col, index2) in row" :key="`cuat2-${index2}`" :class="index2 != 0 ? 'text-center align-middle' : 'align-middle'">
                                                {{ col }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <chart-pie 
                                :chart-data="exposedPopulationCuatData.percentage_x_category"
                                title="Población Expuesta con CUAT"
                                color-line="blue"
                                ref="exposedPopulationCuat"/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Población Expuesta con CUAP" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col><vue-advanced-select :disabled="isLoading" v-model="exposedPopulationCuapSelected" :options="selectBar" :searchable="true" name="exposedPopulationCuapSelected">
                            </vue-advanced-select></b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="6">
                            <b>Total Expuestos con CUAP {{ exposedPopulationCuapData.percentage_x_category.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-6" style="padding-bottom: 15px;">
                            <div class="table-responsive" v-if="exposedPopulationCuapData.total.length > 0">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">Descripción</th>
                                            <th class="text-center align-middle">Total</th>
                                            <th class="text-center align-middle">% Respecto a la población</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, index) in exposedPopulationCuapData.total" :key="`cuap-${index}`">
                                            <td v-for="(col, index2) in row" :key="`cuap2-${index2}`" :class="index2 != 0 ? 'text-center align-middle' : 'align-middle'">
                                                {{ col }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <chart-pie 
                                :chart-data="exposedPopulationCuapData.percentage_x_category"
                                title="Población Expuesta con CUAP"
                                color-line="red"
                                ref="exposedPopulationCuap"/>
                        </div>
                    </b-row>
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
                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Diagnóstico Normal</h4>
                        </b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="6">
                            <b>Total Normal {{ exposedPopulationaudiologicalConditionData.normal.percentage_x_category.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-6" style="padding-bottom: 15px;">
                            <div class="table-responsive" v-if="exposedPopulationaudiologicalConditionData.normal.total.length > 0">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">Descripción</th>
                                            <th class="text-center align-middle">Total</th>
                                            <th class="text-center align-middle">% Respecto a la población</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, index) in exposedPopulationaudiologicalConditionData.normal.total" :key="`dx-normal-${index}`">
                                            <td v-for="(col, index2) in row" :key="`dx2-normal-${index2}`" :class="index2 != 0 ? 'text-center align-middle' : 'align-middle'">
                                                {{ col }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <chart-pie 
                                :chart-data="exposedPopulationaudiologicalConditionData.normal.percentage_x_category"
                                title="DX Audiometrias - Diagnóstico Normal"
                                color-line="red"
                                ref="exposedPopulationaudiologicalConditionNormal"/>
                        </div>
                    </b-row>

                    <b-row>
                        <b-col class="text-center" style="padding-bottom: 15px;">
                            <h4>Diagnóstico Alterado</h4>
                        </b-col>
                    </b-row>
                    <b-row align-h="end">
                        <b-col cols="6">
                            <b>Total Alterado {{ exposedPopulationaudiologicalConditionData.alterada.percentage_x_category.datasets.count }} </b>
                        </b-col>
                    </b-row>
                    <b-row>
                        <div class="col-md-6" style="padding-bottom: 15px;">
                            <div class="table-responsive" v-if="exposedPopulationaudiologicalConditionData.alterada.total.length > 0">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">Descripción</th>
                                            <th class="text-center align-middle">Total</th>
                                            <th class="text-center align-middle">% Respecto a la población</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, index) in exposedPopulationaudiologicalConditionData.alterada.total" :key="`dx-alterada-${index}`">
                                            <td v-for="(col, index2) in row" :key="`dx2-alterada-${index2}`" :class="index2 != 0 ? 'text-center align-middle' : 'align-middle'">
                                                {{ col }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <chart-pie 
                                :chart-data="exposedPopulationaudiologicalConditionData.alterada.percentage_x_category"
                                title="DX Audiometrias - Diagnóstico Alterado"
                                color-line="red"
                                ref="exposedPopulationaudiologicalConditionAlterada"/>
                        </div>
                    </b-row>
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
                    <b-row>
                        <div class="col-md-6" style="padding-bottom: 15px;">
                            <chart-bar 
                                :chart-data="exposedPopulationData"
                                title="Población Expuesta"
                                ref="exposedPopulation"/>
                        </div>
                        <div class="col-md-6">
                            <chart-pie 
                                :chart-data="exposedPopulationData"
                                title="Población Expuesta"
                                color-line="red"
                                ref="exposedPopulationPie"/>
                        </div>
                    </b-row>
                </b-card>
            </b-col>
        </b-row>

        <div class="row float-right pt-10 pr-10">
            <template>
                <b-btn variant="default" :to="{name: 'biologicalmonitoring-audiometry'}">Atras</b-btn>
            </template>
        </div>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import ChartPie from '@/components/ECharts/ChartPie.vue';
import ChartBar from '@/components/ECharts/ChartBar.vue';
import FilterGeneral from '@/components/Filters/FilterGeneral.vue';

export default {
    name: 'audiometry-informs',
    metaInfo: {
        title: 'Audiometria - Informes'
    },
    components:{
        VueAdvancedSelect,
        ChartPie,
        ChartBar,
        FilterGeneral
    },
    data () {
        return {
            filters: [],
            selectBar: [],
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
                deal: {
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
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_headquarter_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_area_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_process_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                deal: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_position_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                }
            },
            exposedPopulationCuatSelected: 'employee_regional_id',
            exposedPopulationCuap: {
                employee_regional_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_headquarter_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_area_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_process_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                deal: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                },
                employee_position_id: {
                    total: [],
                    percentage_x_category: {
                        labels: [],
                        datasets: []
                    }
                }
            },
            exposedPopulationCuapSelected: 'employee_regional_id',
            exposedPopulationaudiologicalCondition: {
                employee_regional_id: {
                    normal: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        }
                    },
                    alterada: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        } 
                    }
                },
                employee_headquarter_id: {
                    normal: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        }
                    },
                    alterada: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        } 
                    }
                },
                employee_area_id: {
                    normal: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        }
                    },
                    alterada: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        } 
                    }
                },
                employee_process_id: {
                    normal: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        }
                    },
                    alterada: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        } 
                    }
                },
                deal: {
                    normal: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        }
                    },
                    alterada: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        } 
                    }
                },
                employee_position_id: {
                    normal: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        }
                    },
                    alterada: {
                        total: [],
                        percentage_x_category: {
                            labels: [],
                            datasets: {
                                count: []
                            }
                        } 
                    }
                }
            },
            exposedPopulationaudiologicalConditionSelected: 'employee_regional_id',
        }
    },
    created(){
        this.fetchSelect('selectBar', '/selects/multiselectBar')
        this.fetch()
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
    watch: {
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
        fetch()
        {
            if (!this.isLoading)
            {
                //console.log('buscando...')
                this.isLoading = true;

                axios.post('/biologicalmonitoring/audiometry/informs', this.filters)
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