<template>
    <div class="col-md-12">
        <b-form-row style="padding-top: 15px;">
            <b-form-feedback class="d-block" v-if="form.errorsFor(`causes`)" style="padding-bottom: 10px;">
            {{ form.errorsFor(`causes`) }}
            </b-form-feedback>
            <!--<perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">-->
                <template v-for="(cause, index) in causes">
                <b-card no-body class="mb-2 border-secondary" :key="cause.key" style="width: 100%;">
                    <b-card-header class="bg-secondary">
                    <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> {{ causes[index].description ? causes[index].description : `Nuevo Causa principal ${index + 1}` }}</b-col>
                        <b-col cols="2">
                        <div class="float-right">
                            <b-button-group>
                                <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + cause.key+'-1'" variant="link">
                                    <span class="collapse-icon"></span>
                                </b-btn>
                            </b-button-group>
                        </div>
                        </b-col>
                    </b-row>
                    </b-card-header>
                    <b-collapse :id="`accordion${cause.key}-1`" visible :accordion="`accordion-123`">
                    <b-card-body>
                        <b-form-row>
                            <vue-textarea class="col-md-12" v-model="causes[index].description" disabled name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.description`)" rows="1"></vue-textarea>
                        </b-form-row>
                        <!--<b-form-row>
                            <div class="col-md-12">
                                <div class="float-right" style="padding-top: 10px;">
                                <b-btn variant="primary" @click.prevent="addCauseSecondary(index)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa Secundaria</b-btn>
                                </div>
                            </div>
                        </b-form-row>-->
                        <b-form-row style="padding-top: 15px;">
                            <b-form-feedback class="d-block" v-if="form.errorsFor(`causes.${index}.secondary`)" style="padding-bottom: 10px;">
                                {{ form.errorsFor(`causes.${index}.secondary`) }}
                            </b-form-feedback>
                            <template v-for="(secondaryCause, index2) in cause.secondary">
                                <b-card no-body class="mb-2 border-secondary" :key="secondaryCause.key" style="width: 100%;">
                                    <b-card-header class="bg-secondary">
                                        <b-row>
                                            <b-col cols="10" class="d-flex justify-content-between"> {{ causes[index].secondary[index2].description ? causes[index].secondary[index2].description : `Nueva Causa Secundaria ${index2 + 1}` }}</b-col>
                                            <b-col cols="2">
                                                <div class="float-right">
                                                    <b-button-group>
                                                       <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + secondaryCause.key+'-1'" variant="link">
                                                        <span class="collapse-icon"></span>
                                                        </b-btn>
                                                        <!-- <b-btn @click.prevent="removeCauseSecondary(index, index2)" 
                                                        
                                                        size="sm" 
                                                        variant="secondary icon-btn borderless"
                                                        v-b-tooltip.top title="Eliminar Causa Secundaria">
                                                            <span class="ion ion-md-close-circle"></span>
                                                        </b-btn>-->
                                                    </b-button-group>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-card-header>
                                    <b-collapse :id="`accordion${secondaryCause.key}-1`" visible :accordion="`accordion-1234`">
                                        <b-card-body>
                                            <b-form-row>
                                                <vue-textarea class="col-md-12" v-model="causes[index].secondary[index2].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.secondary.${index2}.description`)" disabled rows="1"></vue-textarea>
                                            </b-form-row>
                                            <b-form-row>
                                                <div class="col-md-12">
                                                    <div class="float-right" style="padding-top: 10px;">
                                                        <b-btn variant="primary" @click.prevent="addTertiary(index, index2)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa</b-btn>
                                                    </div>
                                                </div>
                                            </b-form-row>
                                            <b-form-row style="padding-top: 15px;">
                                                <b-form-feedback  class="d-block" v-if="form.errorsFor(`causes.${index}.secondary.${index2}.tertiary`)" style="padding-bottom: 10px;">
                                                {{ form.errorsFor(`causes.${index}.secondary.${index2}.tertiary`) }}
                                                </b-form-feedback>

                                                <template v-for="(item, index3) in secondaryCause.tertiary">
                                                    <div :key="item.key" class="col-md-12">
                                                        <b-form-row>
                                                            <div class="col-md-12">
                                                                <div class="float-right">
                                                                    <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeTertiary(index, index2, index3)"><span class="ion ion-md-close-circle"></span></b-btn>
                                                                </div>
                                                            </div>
                                                            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="causes[index].secondary[index2].tertiary[index3].category_id" :error="form.errorsFor(`causes.${index}.secondary.${index2}.tertiary.${index3}.category_id`)" label="Categoria" name="category_id" placeholder="Seleccione la categoria" :url="awCategoriesData" :parameters="{section: causes[index].secondary[index2].section_id}" :multiple="false" :selected-object="causes[index].secondary[index2].tertiary[index3].multiselect_category_id">
                                                                            </vue-ajax-advanced-select>
                                                            <vue-ajax-advanced-select :disabled="viewOnly || !causes[index].secondary[index2].tertiary[index3].category_id" class="col-md-6" v-model="causes[index].secondary[index2].tertiary[index3].item_id" :error="form.errorsFor(`causes.${index}.secondary.${index2}.tertiary.${index3}.items_id`)" label="Item" name="item_id" placeholder="Seleccione el item" :url="awCategoriesItemsData" :parameters="{category: causes[index].secondary[index2].tertiary[index3].category_id}" :multiple="false" :selected-object="causes[index].secondary[index2].tertiary[index3].multiselect_item_id">
                                                                            </vue-ajax-advanced-select>
                                                        </b-form-row>
                                                    </div>
                                                </template>
                                            </b-form-row>
                                        </b-card-body>
                                    </b-collapse>
                                </b-card>
                            </template>
                        </b-form-row>
                    </b-card-body>
                    </b-collapse>
                </b-card>
                </template>
            <!--</perfect-scrollbar>-->
        </b-form-row>
    </div>
</template>

<script>
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
    components: {
        VueTextarea,
        VueInput,
        PerfectScrollbar,
        VueAjaxAdvancedSelect
        //CausesExport
    },
    props: {
        viewOnly: { type: Boolean, default: false },
        form: { type: Object, required: true },
        isEdit: { type: Boolean, default: false },
        causes: {
			default() {
				return {
					causes: [
                        {
                            key: new Date().getTime(),
                            description: 'Causas Inmediatas',
                            secondary: []
                        },
                        {
                            key: new Date().getTime(),
                            description: 'Causas Básicas / Raíz',
                            secondary: []
                        }
                    ],
                    delete: {
                        causes: [],
                        secondary: [],
                        tertiary: []
                    }
				};
			}
		}
    },
    watch: {
        causes() {
            this.loading = false;
            this.$emit('input', this.causes);
        }
    },
    data() {
        return {
            awCategoriesData: '/selects/awCategories',
            awCategoriesItemsData: '/selects/awItems'
        };
    },
    methods: {
        addCause() {
            this.causes.push({
                key: new Date().getTime(),
                description: '',
                secondary: []
            })
        },
        removeCause(index)
        {
            if (this.causes[index].id != undefined)
                this.delete.causes.push(this.causes[index].id)

            this.causes.splice(index, 1)
        },
        addCauseSecondary(index)
        {
            this.causes[index].secondary.push({
                key: new Date().getTime(),
                description: '',
                tertiary: []
            })
        },
        removeCauseSecondary(indexObj, index)
        {
            if (this.causes[indexObj].secondary[index].id != undefined)
                this.delete.secondary.push(this.causes[indexObj].secondary[index].id)

            this.causes[indexObj].secondary.splice(index, 1)
        },
        addTertiary(indexObj, indexSub) 
        {
            this.causes[indexObj].secondary[indexSub].tertiary.push({
                key: new Date().getTime(),
                description: ''
            })
        },
        removeTertiary(indexObj, indexSub, index)
        {
            if (this.causes[indexObj].secondary[indexSub].tertiary[index].id != undefined)
                this.delete.tertiary.push(this.causes[indexObj].secondary[indexSub].tertiary[index].id)

            this.causes[indexObj].secondary[indexSub].tertiary.splice(index, 1)
        }
    },
    mounted() {
        //this.initTree();
    }
};
</script>


<style lang="scss">
body {
background-color: #eee;
}

.node--internal {
    max-width: 100%;
    max-height: 100%;
}

.node--leaf {
    max-width: 100%;
    max-height: 100%;
}

.node circle {
fill: #fff;
stroke: steelblue;
stroke-width: 3px;
}

.node text { font: 12px sans-serif; }

.link {
fill: none;
stroke: #fff;
stroke-width: 2px;
}
</style>