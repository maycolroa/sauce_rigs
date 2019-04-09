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
                                    <b-col cols="6" :key="index" v-if="item.active && index != 'dateRange'"><vue-advanced-select  v-model="filtersSelected[index]" :multiple="true" :options="item.data" :searchable="true" :name="item.name" :label="item.label" :disabled="isDisabled" :filterTypeSearch="true" @updateFilterTypeSearch="setFilterTypeSearch($event, item.name)">
                                    </vue-advanced-select></b-col>
                                </template>
                            </b-row>
                            <b-row v-if="filters.dateRange.active">
                                <vue-datepicker-range class="col-md-12" v-model="filtersSelected.dateRange" :label="filters.dateRange.label" :name="filters.dateRange.name" :disabled="isDisabled">
                                    </vue-datepicker-range>
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
                businesses: {
                    label: 'Centros de costo',
                    name: 'businesses',
                    data: [],
                    active: false,
                    ready: false
                },
                positions: {
                    label: 'Cargo',
                    name: 'positions',
                    data: [],
                    active: false,
                    ready: false
                },
                years: {
                    label: 'Años',
                    name: 'years',
                    data: [],
                    active: false,
                    ready: false
                },
                dateRange: {
                    label: 'Rango de fecha',
                    name: 'date_range',
                    active: false,
                    ready: false
                },
                evaluationsObjectives: {
                    label: 'Objetivos',
                    name: 'evaluationsObjectives',
                    data: [],
                    active: false,
                    ready: false
                },
                evaluationsSubobjectives: {
                    label: 'Subobjetivos',
                    name: 'evaluationsSubobjectives',
                    data: [],
                    active: false,
                    ready: false
                },
            },
            filtersSelected: {
                regionals: [],
                headquarters: [],
                areas: [],
                processes: [],
                businesses: [],
                positions: [],
                years: [],
                dateRange: '',
                evaluationsObjectives: [],
                evaluationsSubobjectives: [],
                filtersType: {
                    regionals: 'IN',
                    headquarters: 'IN',
                    areas: 'IN',
                    processes: 'IN',
                    businesses: 'IN',
                    positions: 'IN',
                    years: 'IN',
                    evaluationsObjectives: 'IN',
                    evaluationsSubobjectives: 'IN'
                }
            }
        }
    },
    created(){
        if (this.config.filters != undefined)
        {
            for(var i in this.config.filters)
            {
                let item = this.config.filters[i]

                if (this.filters[item.key] != undefined)
                {
                    this.filters[item.key].active = true

                    if (item.key == 'dateRange')
                    {
                        this.filters[item.key].ready = true
                    }
                    else
                        this.fetchFilterSelect(item.key, item.url)
                }
            }
        }
    },
    mounted() {
        this.$emit('input', this.filtersSelected)
    },
    computed: {
        /*filtersActive()
        {      
            let data = {}

            for(var i in this.filtersSelected)
            {
                if (this.filtersSelected[i].length > 0)
                {
                    this.$set(data, i, this.filtersSelected[i])
                }
            }

            return data;
        },*/
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
        'filters.businesses.data'() {
            this.updateFilterData('businesses')
        },
        'filters.positions.data'() {
            this.updateFilterData('positions')
        },
        'filters.years.data'() {
            this.updateFilterData('years')
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
        'filtersSelected.businesses'() {
            this.updateFilterTable('businesses')
        },
        'filtersSelected.positions'() {
            this.updateFilterTable('positions')
        },
        'filtersSelected.years'() {
            this.updateFilterTable('years')
        },
        'filtersSelected.dateRange'() {
            this.updateFilterTable('dateRange')
        },
        'filtersSelected.evaluationsObjectives'() {
            this.updateFilterTable('evaluationsObjectives')
        },
        'filtersSelected.evaluationsSubobjectives'() {
            this.updateFilterTable('evaluationsSubobjectives')
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
            //this.filtersSelected[key] = this.filters[key].data
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