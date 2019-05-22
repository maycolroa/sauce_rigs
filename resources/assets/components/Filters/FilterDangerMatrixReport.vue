<template>
    <div>
        <div style="padding: 10px;">
            <b-btn variant="secondary icon-btn" @click="showFilterModal()"><span class="fas fa-filter"></span></b-btn>
        </div>

        <!-- Modal template -->
        <b-modal ref="filterModal" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Filtros
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <b-row>
                                <template v-for="(item, index) in filters"> 
                                    <b-col cols="12" :key="index" v-if="item.active"><vue-advanced-select  v-model="filtersSelected[index]" :multiple="true" :options="item.data" :searchable="true" :name="item.name" :label="item.label" :disabled="isDisabled" :filterTypeSearch="true" @updateFilterTypeSearch="setFilterTypeSearch($event, item.name)">
                                    </vue-advanced-select></b-col>
                                </template>
                            </b-row>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideFilterModal()">Cerrar</b-btn>
            </div>
        </b-modal>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueDatepickerRange from "@/components/Inputs/VueDatepickerRange.vue";
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import FilterConfig from '@/filterconfig/';

export default {
    components:{
        VueAdvancedSelect,
        VueDatepickerRange,
        SweetModal,
        SweetModalTab
    },
    props: {
        configName: {type: String, required: true},
        config: {type: Object, default: function(){
            return FilterConfig.get(this.configName);
        }},
        isDisabled: {
            type: Boolean,
            default: false
        },
        modelId: {type: [Number, String], default: null}
    },
    data () {
        return {
            filters: {
                regionals: {
                    label: 'Regionales',
                    name: 'regionals',
                    data: [],
                    active: false,
                    ready: false
                },
                headquarters: {
                    label: 'Sedes',
                    name: 'headquarters',
                    data: [],
                    active: false,
                    ready: false
                },
                processes: {
                    label: 'Procesos',
                    name: 'processes',
                    data: [],
                    active: false,
                    ready: false
                },
                areas: {
                    label: 'Áreas',
                    name: 'areas',
                    data: [],
                    active: false,
                    ready: false
                },
                dangers: {
                    label: 'Peligros',
                    name: 'dangers',
                    data: [],
                    active: false,
                    ready: false
                },
                matrix: {
                    label: 'Matriz de peligros',
                    name: 'matrix',
                    data: [],
                    active: false,
                    ready: false
                }
            },
            filtersSelected: {
                regionals: [],
                headquarters: [],
                areas: [],
                processes: [],
                dangers: [],
                matrix: [],
                filtersType: {
                    regionals: 'IN', 
                    headquarters: 'IN',
                    areas: 'IN',
                    processes: 'IN',
                    dangers: 'IN',
                    matrix: 'IN'
                }
            }
        }
    },
    created(){
        if (this.config.filters != undefined)
        {
            axios.post('/administration/configurations/locationLevelForms/getConfModule')
            .then(data => {
                if (Object.keys(data.data).length > 0)
                {
                    let inputs = data.data

                    for(var i in this.config.filters)
                    {
                        let item = this.config.filters[i]

                        if (item.key == 'regionals' && inputs.regional == 'NO')
                            continue;
                        if (item.key == 'headquarters' && inputs.headquarter == 'NO')
                            continue;
                        if (item.key == 'processes' && inputs.process == 'NO')
                            continue;
                        if (item.key == 'areas' && inputs.area == 'NO')
                            continue;

                        if (this.filters[item.key] != undefined)
                        {
                            this.filters[item.key].active = true

                            this.fetchFilterSelect(item.key, item.url)
                        }
                    }
                }
            })
            .catch(error => {
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
        }
    },
    mounted() {
        //this.$emit('input', this.filtersSelected)
    },
    computed: {
    },
    watch: {
        'filters.regionals.data'() {
            this.updateFilterData('regionals')
        },
        'filters.headquarters.data'() {
            this.updateFilterData('headquarters')
        },
        'filters.areas.data'() {
            this.updateFilterData('areas')
        },
        'filters.processes.data'() {
            this.updateFilterData('processes')
        },
        'filters.dangers.data'() {
            this.updateFilterData('dangers')
        },
        'filters.matrix.data'() {
            this.updateFilterData('matrix')
        },
        'filtersSelected.regionals'() {
            this.updateFilterTable('regionals')
        },
        'filtersSelected.headquarters'() {
            this.updateFilterTable('headquarters')
        },
        'filtersSelected.areas'() {
            this.updateFilterTable('areas')
        },
        'filtersSelected.processes'() {
            this.updateFilterTable('processes')
        },
        'filtersSelected.dangers'() {
            this.updateFilterTable('dangers')
        },
        'filtersSelected.matrix'() {
            this.updateFilterTable('matrix')
        },
    },
    methods: {
        fetchFilterSelect(key, url)
        {
            let paramId = this.modelId ? { modelId: this.modelId } : {}
            GlobalMethods.getDataMultiselect(url, paramId)
            .then(response => {
                this.filters[key].data = response;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
        },
        updateFilterData(key)
        {
            setTimeout(() => {
                this.filters[key].ready = true
            }, 1000)
        },
        updateFilterTable(key)
        {
            if (this.filters[key].ready)
                this.$emit('input', this.filtersSelected)
        },
        showFilterModal () {
            this.$refs.filterModal.show()
        },
        hideFilterModal () {
            this.$refs.filterModal.hide()
        },
        setFilterTypeSearch(event, key) {
            this.filtersSelected.filtersType[key] = event
        }
    }
}
</script>