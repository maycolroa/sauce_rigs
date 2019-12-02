<template>
    <div>
        <div style="padding: 10px;">
            <center>
                <b-btn class="icon-btn" @click="showFilterModal()" v-b-tooltip.top title="Abrir Filtros"><img :src="colorIcon" class="img-fluid"/></b-btn>&nbsp;&nbsp;
                <b-btn variant="secondary icon-btn" @click="cleanFilters()" v-b-tooltip.top title="Limpiar Filtros">
                    <img src="/images/LimpiarFiltros.png" class="img-fluid" ref="clear" @mouseover="changeClassImage('clear', 'clear_hover')"/>
                    <img src="/images/LimpiarFiltros_hover.png" class="img-fluid imgHidden" ref="clear_hover" @mouseleave="changeClassImage('clear_hover', 'clear')"/>
                </b-btn>
            </center>
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
                            <b-row v-for="(item, index) in filters" :key="index">
                                <b-col>
                                    <vue-advanced-select
                                        v-if="item.type == 'select'"
                                        :ref="item.key"
                                        v-model="filtersSelected[index]" :multiple="true" :options="item.data" :searchable="true" :name="item.key" :label="keywordCheck(item.label, item.label)" :disabled="isDisabled || !ready" :filterTypeSearch="true" @updateFilterTypeSearch="setFilterTypeSearch($event, item.key)"
                                        :filter-type-search-value="filtersSelected.filtersType[index]">
                                    </vue-advanced-select>
                                </b-col>
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
            ready: false,
            filters: {},
            filtersSelected: {
                filtersType: {}
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
                        if (item.key == 'macroprocesses' && inputs.process == 'NO')
                            continue;
                        if (item.key == 'areas' && inputs.area == 'NO')
                            continue;

                        this.$set(this.filters, item.key, item)
                        
                        if (item.type == 'select')
                        {
                            this.$set(this.filters[item.key], 'data', [])
                            this.$set(this.filtersSelected, item.key, [])
                            this.$set(this.filtersSelected.filtersType, item.key, 'IN')
                            this.fetchFilterSelect(item.key, item.url)
                        }
                        else if (item.type == 'dateRange')
                        {
                            this.$set(this.filtersSelected, item.key, '')
                        }
                    }

                    setTimeout(() => {
                        this.ready = true
                    }, 3000)
                }
            })
            .catch(error => {
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
        }
    },
    mounted() {
        setTimeout(() => {
            this.getStateFilters()
        }, 3000)
    },
    computed: {
        colorIcon()
        {
            let color = '/images/Filtrar.png'

            _.forIn(this.filtersSelected, (value, key) => {
                
                if (key != 'filtersType')
                {
                    if (typeof value === 'string')
                    {
                        if (value != '')
                            color = '/images/Filtrar_hover.png'
                    }
                    else
                    {
                        if (this.filtersSelected[key].length > 0)
                            color = '/images/Filtrar_hover.png'
                    }
                }
            });

            return color
        }
    },
    watch: {
        filtersSelected: {
            handler(val) {
                if (this.ready)
                    this.emitFilters()
            },
            deep: true
        }
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
        emitFilters(key)
        {
            this.setStateFilters()
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
        },
        setStateFilters()
        {
            let data = {
                url: this.$route.path,
                filters: this.filtersSelected
            }

            axios.post(`/setStateFilters`, data)
                .then(response => {
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                    this.$router.go(-1);
                });
        },
        getStateFilters()
        {
            axios.post(`/getStateFilters`, { url: this.$route.path })
                .then(response => {

                    if (response.data)
                    {
                        this.ready = false
                        
                        _.forIn(response.data, (value, key) => {

                            if (key != 'filtersType')
                            {
                                if (typeof value === 'string')
                                {
                                    this.filtersSelected[key] = value
                                }
                                else
                                {
                                    _.forIn(value, (item, keyItem) => {
                                        this.filtersSelected[key].push({ name: item.name, value: item.value })
                                    })
                                }

                                if (this.$refs[key])
                                    this.$refs[key][0].refreshData()
                            }
                            else
                            {
                                _.forIn(value, (item, keyItem) => {
                                    this.filtersSelected.filtersType[keyItem] = item
                                })
                            }
                        })

                        setTimeout(() => {
                            this.$emit('input', this.filtersSelected)
                            this.ready = true
                        }, 2000)
                    }
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                    this.$router.go(-1);
                });
        },
        cleanFilters()
        {
            this.ready = false

            _.forIn(this.filtersSelected, (value, key) => {
                
                if (key != 'filtersType')
                {
                    if (typeof value === 'string')
                    {
                        this.filtersSelected[key] = ''
                    }
                    else
                    {
                        this.filtersSelected[key].splice(0)
                    }
                }
                else
                {
                    _.forIn(value, (item, keyItem) => {
                        this.filtersSelected.filtersType[keyItem] = 'IN'
                    })
                }

            });

            setTimeout(() => {
                this.emitFilters()
                this.ready = true
            }, 2000)
        },
        changeClassImage(image, imageHover) {
            this.$refs[image].classList.add("imgHidden");
            this.$refs[imageHover].classList.remove("imgHidden");
        }
    }
}
</script>