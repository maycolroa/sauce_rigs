<template>
    <div class="col-md-12">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Actividades del plan de acción</p>
        </blockquote>
        <b-form-row>
            <div class="col-md-12" v-if="!viewOnly">
            <div class="float-right" style="padding-top: 10px;">
                <b-btn variant="primary" @click.prevent="addActiviy()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Actividad</b-btn>
            </div>
            </div>
        </b-form-row>
        <b-form-row v-if="actionPlan.activities.length > 0">
            <vue-input class="col-md-12" v-model="search" label="Buscar Actividad" type="text" name="search" placeholder="Buscar Actividad" append='<span class="fas fa-search"></span>'></vue-input>
        </b-form-row>
        <b-form-row style="padding-top: 15px;">
            <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                <template v-for="(activity, index) in actionPlan.activities">
                    <b-card no-body class="mb-2 border-secondary" :key="activity.key" style="width: 100%;" v-show="showActivity(activity.description)">
                    <b-card-header class="bg-secondary">
                        <b-row>
                        <b-col cols="10" class="d-flex justify-content-between text-white"> Actividad #{{ index + 1 }}</b-col>
                        <b-col cols="2">
                            <div class="float-right">
                            <b-button-group>
                                <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + activity.key+'-1'" variant="link">
                                <span class="collapse-icon"></span>
                                </b-btn>
                                <b-btn @click.prevent="removeActivity(index)" 
                                v-if="!viewOnly"
                                size="sm" 
                                variant="secondary icon-btn borderless"
                                v-b-tooltip.top title="Eliminar Actividad">
                                <span class="ion ion-md-close-circle"></span>
                                </b-btn>
                            </b-button-group>
                            </div>
                        </b-col>
                        </b-row>
                    </b-card-header>
                    <b-collapse :id="`accordion${activity.key}-1`" visible :accordion="`accordion-${prefixIndex}`">
                        <b-card-body>
                            <b-form-row>
                                <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="activity.description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.description`)"></vue-textarea>
                                <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="activity.responsible_id" :selected-object="activity.multiselect_responsible" name="responsible_id" label="Responsable" placeholder="Seleccione el responsable" :url="userDataUrl" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.responsible_id`)">
                                    </vue-ajax-advanced-select>
                            </b-form-row>
                            <b-form-row>
                                <vue-datepicker :disabled="viewOnly" class="col-md-4" v-model="activity.expiration_date" label="Fecha de vencimiento" placeholder="Seleccione la fecha de vencimiento" name="expiration_date" :disabled-dates="disabledExpirationDate(index)" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.expiration_date`)">
                                    </vue-datepicker>
                                <vue-datepicker :disabled="viewOnly || activity.expiration_date == ''" class="col-md-4" v-model="activity.execution_date" label="Fecha de ejecución" placeholder="Seleccione la fecha de ejecución" name="execution_date" :disabled-dates="disabledExecutionDate(index)" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.execution_date`)">
                                    </vue-datepicker>
                                <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="activity.state" :multiple="false" :options="actionPlanStates" :hide-selected="false" name="state" label="Estado" placeholder="Seleccione el estado" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.state`)">
                                    </vue-advanced-select>
                            </b-form-row>
                        </b-card-body>
                    </b-collapse>
                    </b-card>
                </template>
            </perfect-scrollbar>
        </b-form-row>
    </div>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import VueInput from "@/components/Inputs/VueInput.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";

export default {
    components: {
        VueAjaxAdvancedSelect,
        PerfectScrollbar,
        VueInput,
        VueTextarea,
        VueDatepicker,
        VueAdvancedSelect
    },
    props: {
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },    
        form: { type: Object, required: true },
        prefixIndex: { type: String, default: ''},
        actionPlanStates: {
            type: Array,
            default: function() {
                return [];
            }
        },
        actionPlan: {
            default() {
                return {
                    activities: [],
                    activitiesRemoved: []
                }
            }
        }
    },
    data() {
        return {
            search: '',
            userDataUrl: '/selects/users'
        };
    },
    created()
    {
    },
    mounted() {
    },
    watch: {
        actionPlan()
        {
            this.$emit("input", this.actionPlan);
        },
        'actionPlan.activities': {
            handler(val){
                this.$emit("input", this.actionPlan);
            },
            deep: true
        },
        'actionPlan.activitiesRemoved': {
            handler(val){
                this.$emit("input", this.actionPlan);
            },
            deep: true
        }
    },
    methods: {
        addActiviy() {
            this.actionPlan.activities.push({
                key: new Date().getTime(),
                id: '',
                description: '',
                responsible_id: '',
                execution_date: '',
                expiration_date: '',
                state: '',
                editable: ''
            })
        },
        removeActivity(index) {
            if (this.actionPlan.activities[index].id != '')
                this.actionPlan.activitiesRemoved.push(this.actionPlan.activities[index])
            this.actionPlan.activities.splice(index, 1)
        },
        showActivity(activity) {
            if (this.search != '')
            {
                return activity.toLowerCase().indexOf(this.search.toLowerCase()) !== -1 ? true : false
            }
            else
                return true;
        },
        disabledExecutionDate(index) {
            if (this.actionPlan.activities[index].expiration_date)
            {
                let toDate = new Date()
                toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())
                
                let fromDate = new Date(this.actionPlan.activities[index].expiration_date)
                fromDate = new Date(fromDate.getFullYear(), fromDate.getMonth(), fromDate.getDate())

                return {
                    to: toDate,
                    from: fromDate
                }
            }
            else
            {
                let toDate = new Date()
                toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

                return {
                    to: toDate
                }
            }
        },
        disabledExpirationDate(index) {
            if (this.actionPlan.activities[index].execution_date)
            {
                let toDate = new Date(this.actionPlan.activities[index].execution_date)
                toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

                return {
                    to: toDate
                }
            }
            else
            {
                let toDate = new Date()
                toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

                return {
                    to: toDate
                }
            }
        }
    }
}
</script>