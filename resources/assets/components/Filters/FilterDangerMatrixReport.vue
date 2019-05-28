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
                            <b-row v-for="(item, index) in filters" :key="index">
                                <b-col>
                                    <vue-advanced-select
                                        v-if="item.type == 'select'"
                                        v-model="filtersSelected[index]" :multiple="true" :options="item.data" :searchable="true" :name="item.key" :label="item.label" :disabled="isDisabled" :filterTypeSearch="true" @updateFilterTypeSearch="setFilterTypeSearch($event, item.key)">
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
        //this.$emit('input', this.filtersSelected)
    },
    computed: {
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