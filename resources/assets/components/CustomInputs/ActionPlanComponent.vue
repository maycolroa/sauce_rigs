<template>
    <div class="col-md-12">
        <blockquote class="blockquote text-center" v-if="!isEditItem">
            <p class="mb-0">Actividades del plan de acción</p>
        </blockquote>
        <b-form-row>
            <div class="col-md-12" v-if="!viewOnly && !isEditItem">
            <div class="float-right" style="padding-top: 10px;">
                <b-btn variant="primary" @click.prevent="addActiviy()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Actividad</b-btn>
            </div>
            </div>
        </b-form-row>
        <b-form-row v-if="actionPlan.activities.length > 0 && !isEditItem">
            <vue-input class="col-md-12" v-model="search" label="Buscar Actividad" type="text" name="search" placeholder="Buscar Actividad" append='<span class="fas fa-search"></span>'></vue-input>
        </b-form-row>
        <b-form-row style="padding-top: 15px;">
            <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                <template v-for="(activity, index) in actionPlan.activities">
                    <b-card no-body class="mb-2 border-secondary" :key="activity.key" style="width: 100%;" v-show="showActivity(activity.description)">
                    <b-card-header class="bg-secondary">
                        <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> Actividad #{{ index + 1 }}</b-col>
                        <b-col cols="2">
                            <div class="float-right">
                            <b-button-group>
                                <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + activity.key+'-1'" variant="link">
                                <span class="collapse-icon"></span>
                                </b-btn>
                                <b-btn @click.prevent="removeActivity(index)" 
                                v-if="!viewOnly && activity.editable != 'NO' && activity.edit_all && !isEditItem"
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
                            <b-form-row v-show="viewOnly || isEdit">
                                <vue-input :disabled="viewOnly || isEdit" class="col-md-12" v-model="activity.user_creator_name" label="Usuario Creador" name="user_creator_name" placeholder="Usuario Creador" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.user_creator_name`)"></vue-input>
                            </b-form-row>
                            <b-form-row>
                                <vue-textarea :disabled="viewOnly || activity.editable == 'NO' || !activity.edit_all" class="col-md-12" v-model="activity.description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.description`)"></vue-textarea>
                                 <vue-textarea :disabled="true" class="col-md-12" v-model="activity.detail_procedence" label="Detalle de procedencia" name="detail_procedence" placeholder="Detalle de procedencia" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.detail_procedence`)"></vue-textarea>
                            </b-form-row>
                            <b-form-row>
                               <vue-ajax-advanced-select :disabled="viewOnly || !activity.edit_all" class="col-md-12" v-model="activity.responsible_id" :selected-object="activity.multiselect_responsible" name="responsible_id" label="Responsable" placeholder="Seleccione el responsable" :url="userDataUrl" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.responsible_id`)">
                                    </vue-ajax-advanced-select>
                            </b-form-row>
                            <b-form-row>
                                <vue-datepicker :disabled="viewOnly || !activity.edit_all" class="col-md-4" v-model="activity.expiration_date" label="Fecha de vencimiento" placeholder="Seleccione la fecha de vencimiento" name="expiration_date" :disabled-dates="disabledExpirationDate(index)" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.expiration_date`)">
                                    </vue-datepicker>
                                <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="activity.state" :multiple="false" :options="actionPlanStates" :hide-selected="false" name="state" label="Estado" placeholder="Seleccione el estado" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.state`)">
                                    </vue-advanced-select>
                                <vue-datepicker :disabled="viewOnly || activity.expiration_date == '' || activity.state == 'Pendiente'" class="col-md-3" v-model="activity.execution_date" label="Fecha de ejecución" placeholder="Seleccione la fecha de ejecución" name="execution_date" :disabled-dates="disabledExecutionDate(index)" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.execution_date`)">
                                    </vue-datepicker> 
                                <b-btn variant="outline-primary icon-btn borderless" v-if="activity.expiration_date == '' || activity.state == 'Pendiente'" size="sm" v-b-tooltip.top title="Limpiar Fecha" @click.prevent="cleanDate(index)"><span class="ion ion-md-close-circle"></span></b-btn>    
                               <!-- <b-btn @click.prevent="cleanDate(index)" 
                                v-if="activity.expiration_date == '' || activity.state == 'Pendiente'"
                                size="sm" 
                                variant="primary icon-btn borderless"
                                v-b-tooltip.top title="Limpiar Fecha"/>-->                     
                            </b-form-row>
                            <b-form-row>
                                <vue-radio :disabled="viewOnly || !activity.edit_all" :checked="activity.evidence" class="col-md-12" v-model="activity.evidence" :options="siNo" name="evidence" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.evidence`)" label="¿Requiere evidencia?">
                                </vue-radio>
                            </b-form-row>
                            <b-form-row>
                                <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="activity.observation" label="Observación" name="observation" placeholder="Observación" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.observation`)"></vue-textarea>
                            </b-form-row>
                            <b-form-row v-if="activity.evidence == 'SI' && activity.state == 'Ejecutada'">
                                <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
                                    <div v-if="existError(`${prefixIndex}actionPlan.activities.${index}.`)">
                                        <b-form-feedback class="d-block" style="padding-bottom: 10px; text-align: center">
                                            Las evidencias son obligatorias para esta actividad.
                                        </b-form-feedback>
                                    </div>
                                    <b-card-header class="bg-secondary">
                                        <b-row>
                                        <b-col cols="11" class="d-flex justify-content-between"> Evidencias </b-col>
                                        <b-col cols="1">
                                            <div class="float-right">
                                            <b-button-group>
                                                <b-btn href="javascript:void(0)" v-b-toggle="'accordion-file'" variant="link">
                                                <span class="collapse-icon"></span>
                                                </b-btn>
                                            </b-button-group>
                                            </div>
                                        </b-col>
                                        </b-row>
                                    </b-card-header>
                                    <b-collapse :id="`accordion-file`"  :accordion="`accordion-master`">
                                    <b-card-body>
                                        <template v-for="(file, indexF) in activity.evidence_files">
                                        <div :key="file.key">
                                            <b-form-row>
                                                <div class="col-md-12">
                                                    <div class="float-right">
                                                        <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeFile(index, indexF)"><span class="ion ion-md-close-circle"></span></b-btn>
                                                    </div>
                                                </div>
                                                 <vue-input v-if="file.id" :disabled="true" class="col-md-12" v-model="file.file_name" label="Archivo Cargado Actualmente" name="file_name" placeholder="Archivo Cargado"></vue-input>
                                                <vue-file-simple :disabled="viewOnly" :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/administration/actionPlan/download/${file.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`${prefixIndex}actionPlan.activities.${index}.evidence_files.${indexF}.file`)" :maxFileSize="20"/>
                                            </b-form-row>
                                        </div>
                                        </template>

                                        <b-form-row style="padding-bottom: 20px;">
                                        <div class="col-md-12">
                                            <center><b-btn variant="primary" @click.prevent="addFile(index)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn></center>
                                        </div>
                                        </b-form-row>          
                                    </b-card-body>
                                    </b-collapse>
                                </b-card>
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
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";

export default {
    components: {
        VueAjaxAdvancedSelect,
        PerfectScrollbar,
        VueInput,
        VueTextarea,
        VueDatepicker,
        VueAdvancedSelect,
        VueRadio,
        VueFileSimple
    },
    props: {
        module: { type: String, default: '' },
        isEdit: { type: Boolean, default: false },
        isEditItem: { type: Boolean, default: false },
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
        },
        definedActivities: {
            type: Array,
            default: function() {
                return [];
            }
        },
    },
    data() {
        return {
            search: '',
            userDataUrl: this.module == 'Contratista' ? '/selects/usersActionPlanContract' : '/selects/usersActionPlan',
            siNo: [
                {text: 'SI', value: 'SI'},
                {text: 'NO', value: 'NO'}
            ],
        };
    },
    created() {

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
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                id: '',
                description: '',
                responsible_id: '',
                execution_date: '',
                expiration_date: '',
                state: '',
                observation: '',
                editable: '',
                edit_all: true,
                evidence_files: [],
                files_delete: []
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
                    //to: toDate,
                    //from: fromDate
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
        },
        addFile(index) 
        {
            this.actionPlan.activities[index].evidence_files.push({
                key: new Date().getTime(),
                file: ''
        })
        },
        removeFile(indexA,index)
        {
            if (this.actionPlan.activities[indexA].evidence_files[index].id != undefined)
                this.actionPlan.activities[indexA].files_delete.push(this.actionPlan.activities[indexA].evidence_files[index].id)

            this.actionPlan.activities[indexA].evidence_files.splice(index, 1)
        },
        cleanDate(index)
        {
            this.actionPlan.activities[index].execution_date = '';
        },
        existError(index) {
			let keys = Object.keys(this.form.errors.errors)
			let result = false

			if (keys.length > 0)
			{
				for (let i = 0; i < keys.length; i++)
				{
					if (keys[i].indexOf(index) > -1)
					{
						result = true
						break
					}
				}
			}

			return result
		}
    }
}
</script>