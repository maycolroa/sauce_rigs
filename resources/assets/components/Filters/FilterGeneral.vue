<template>
    <div>
        <b-row align-h="end" style="padding: 10px;" v-if="modal">
            <b-col cols="1">
                <b-btn variant="secondary icon-btn" @click="showFilterModal()"><span class="fas fa-filter"></span></b-btn>
            </b-col>
        </b-row>

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
                                <b-col v-if="item.header == undefined">
                                    <vue-advanced-select
                                        v-if="item.type == 'select'"
                                        v-model="filtersSelected[index]" :multiple="true" :options="item.data" :searchable="true" :name="item.key" :label="item.label" :disabled="isDisabled" :filterTypeSearch="true" @updateFilterTypeSearch="setFilterTypeSearch($event, item.key)">
                                    </vue-advanced-select>

                                    <vue-datepicker-range 
                                        v-if="item.type == 'dateRange'"
                                        v-model="filtersSelected[index]" class="col-md-12" :label="item.label" :name="item.key" :disabled="isDisabled">
                                    </vue-datepicker-range>

                                    <vue-input-range
                                        v-if="item.type == 'numberRange'"
                                        v-model="filtersSelected[index]" class="col-md-12" :label="item.label" :name="item.key" :disabled="isDisabled">
                                    </vue-input-range>
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

        <div class="row" v-if="header">
            <template v-for="(item, index) in filters">
                <div class="col-md-6" v-if="item.header != undefined" :key="index">
                    <vue-advanced-select
                        v-if="item.type == 'select'"
                        v-model="filtersSelected[index]" :multiple="true" :options="item.data" :searchable="true" :name="item.key" :label="item.label" :disabled="isDisabled" :filterTypeSearch="true" @updateFilterTypeSearch="setFilterTypeSearch($event, item.key)">
                    </vue-advanced-select>

                    <vue-datepicker-range 
                        v-if="item.type == 'dateRange'"
                        v-model="filtersSelected[index]" class="col-md-12" :label="item.label" :name="item.key" :disabled="isDisabled">
                    </vue-datepicker-range>

                    <vue-input-range
                        v-if="item.type == 'numberRange'"
                        v-model="filtersSelected[index]" class="col-md-12" :label="item.label" :name="item.key" :disabled="isDisabled">
                    </vue-input-range>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueDatepickerRange from "@/components/Inputs/VueDatepickerRange.vue";
import VueInputRange from "@/components/Inputs/VueInputRange.vue";
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import FilterConfig from '@/filterconfig/';

export default {
    components:{
        VueAdvancedSelect,
        VueDatepickerRange,
        VueInputRange,
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
            header: false,
            modal: false,
            filters: {},
            filtersSelected: {
                filtersType: {}
            }
        }
    },
    created(){
        if (this.config.filters != undefined)
        {
            for(var i in this.config.filters)
            {
                let item = this.config.filters[i]

                if (item.permission != undefined && item.permission)
                    if (!auth.can[item.permission])
                        continue;

                if (item.header == undefined)
                    this.modal = true
                else
                    this.header = true

                this.$set(this.filters, item.key, item)

                if (item.type == 'select')
                {
                    this.$set(this.filters[item.key], 'data', [])
                    this.$set(this.filtersSelected, item.key, [])
                    this.$set(this.filtersSelected.filtersType, item.key, 'IN')
                    this.fetchFilterSelect(item.key, item.url)
                }
                else if (item.type == 'dateRange' || item.type == 'numberRange')
                {
                    this.$set(this.filtersSelected, item.key, '')
                }
            }

            setTimeout(() => {
                this.ready = true
            }, 3000)
        }
    },
    mounted() {
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