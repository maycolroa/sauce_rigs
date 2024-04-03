<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <b-form-row>
            <vue-input :disabled="true" class="col-md-12" v-model="driver_info" label="Nombre del Conductor" type="text" name="driver_info" :error="form.errorsFor('driver_info')" placeholder="Nombre del Conductor"></vue-input> 
        </b-form-row>
        <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date_simit" label="Fecha de la consulta inicial en el simit" :full-month-name="true" placeholder="Fecha de la consulta inicial en el simit" :error="form.errorsFor('date_simit')" name="date_simit" :disabled-dates="disabledExpirationDateFrom()">
                      </vue-datepicker>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date" label="Fecha de infracciones" :full-month-name="true" placeholder="Fecha de infracciones" :error="form.errorsFor('date')" name="date" :disabled-dates="disabledExpirationDateFrom()">
                      </vue-datepicker>
        </b-form-row>
        <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.vehicle_id" :error="form.errorsFor('vehicle_id')" :selected-object="form.multiselect_vehicle" name="vehicle_id" label="Vehiculo" placeholder="Seleccione una opción" :url="vehiclesDataUrl" :multiple="false" :parameters="{driver_id: driver}">
                </vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.type_id" name="type_id" :error="form.errorsFor('type_id')" label="Tipo de infracción" placeholder="Seleccione el tipo" :url="typeInfractionDataUrl" :multiple="false" :allowEmpty="true" :selected-object="form.multiselect_type">
            </vue-ajax-advanced-select>
        </b-form-row>
        <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.codes_types" name="codes_types" :error="form.errorsFor('codes_types')" label="Codigos de infracción" placeholder="Seleccione los codigos" :url="typeInfractionCodeDataUrl" :multiple="true" :allowEmpty="true" :selected-object="form.multiselect_code_type" :parameters="{type_id: form.type_id}">
            </vue-ajax-advanced-select>
            <div class="offset-md-2">
                <b-btn @click="showModal('modalActionPlan')" variant="primary" style="height: 50%; margin-top: 3%; margin-left: 5%;"><span class="lnr lnr-bookmark"></span> Plan de acción</b-btn>
            </div>
            <b-modal ref="modalActionPlan" :hideFooter="true" id="modals-default" class="modal-top" size="lg" @hidden="hideModal('modalActionPlan')">
            <div slot="modal-title">
            Plan de acción <br>
            <small class="text-muted">Crea planes de acción.</small>
            </div>

            <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                <action-plan-component
                    :is-edit="isEdit"
                    :view-only="viewOnly"
                    :form="form"
                    :action-plan-states="actionPlanStates"
                    v-model="form.actionPlan"
                    :action-plan="form.actionPlan"/>
            </b-card>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideModal('modalActionPlan')">Cerrar</b-btn>
            </div>
        </b-modal>
        </b-form-row>

        <b-card border-variant="primary" title="Evidencias" class="mb-3 box-shadow-none">
            <b-card-body>
                <template v-for="(evidence, index) in form.evidences">
                    <div :key="evidence.key">
                        <b-form-row>
                            <div class="col-md-12">
                                <div class="float-right">
                                    <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeFile(index)" v-if="!viewOnly"><span class="ion ion-md-close-circle"></span></b-btn>
                                </div>
                            </div>
                        </b-form-row>
                        <vue-file-simple :disabled="viewOnly" :help-text="evidence.id ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/roadsafety/driverInfractions/download/${evidence.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="evidence.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`form.evidences.${index}.file`)" :maxFileSize="20"/>
                    </div>
                </template>
                <div v-if="!viewOnly" class="col-md-12 text-center" style="padding-top: 15px;">
                    <center><b-btn variant="primary" @click.prevent="addFile">Agregar</b-btn></center>
                </div>
            </b-card-body>
        </b-card>

        <div class="row float-right pt-10 pr-10">
            <template>
                <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
                <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
            </template>
        </div>
    </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        driver: { type: [Number, String], default: ''},
        actionPlanStates: {
            type: Array,
            default: function() {
                return [];
            }
        },
        infraction: {
            type: [Array, Object],
            default() {
                return {
                    vehicle_id: '',
                    driver_id: '',
                    date: '',
                    date_simit: '',
                    type_id: '',
                    codes_types: '',
                    evidences: [],
                    actionPlan: {
                        activities: [],
                        activitiesRemoved: []
                    }
                };
            }
        },
    },
    components: {
        VueInput,
        VueDatepicker,
        VueRadio,
        VueFileSimple,
        Form,
        VueTextarea,
        ActionPlanComponent,
        VueAjaxAdvancedSelect
    },
    computed: {
    },
    watch: {
        infraction() {
            this.loading = false;
            this.form = Form.makeFrom(this.infraction, this.method);
        }
    },
    data () {
        return {
            loading: this.isEdit,
            form: Form.makeFrom(this.infraction, this.method),
            driver_info: '',
            vehiclesDataUrl: "/selects/vehicles",
            typeInfractionDataUrl: "/selects/typeInfraction",
            typeInfractionCodeDataUrl: "/selects/typeInfractionCode",
        };
    },
    methods: {
        submit(e) {
            this.loading = true;

            if (!this.isEdit)
            {
                this.form.driver_id = this.driver;
            }

            this.form.clearFilesBinary();
                _.forIn(this.form.evidences, (file, keyFile) => {
                if (file.file)
                    this.form.addFileBinary(`${keyFile}`, file.file);
                });

            this.form
                .submit(e.target.action)
                .then(response => {
                    this.loading = false;
                    this.$router.push({ name: "industrialsecure-roadsafety-drivers" });
                })
                .catch(error => {
                    this.loading = false;
                });
        },
        addFile() {
           this.form.evidences.push({
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                file: ''
            });
        },
        removeFile(index) {
            if (this.form.evidences[index].id != undefined)
                this.form.delete.files.push(this.form.evidences[index].id)
                
           this.form.evidences.splice(index, 1);
        },
        showModal(ref) {
			this.$refs[ref].show();
		},
		hideModal(ref) {
			this.$refs[ref].hide();
		},
        disabledExpirationDateFrom() {

            let toDate = new Date()
            toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

            return {
                from: toDate
            }
            
        },
        disabledExpirationDateTo() {

            let toDate = new Date()
            toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

            return {
                to: toDate
            }
            
        },
        getInfoDriver() {
            axios.get(`/industrialSecurity/roadsafety/drivers/${this.driver ? this.driver : this.form.driver_id}`)
            .then(response => {
                this.driver_info = response.data.data.employee.name;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                this.$router.go(-1);
            });
        }
    },
    created() {
        setTimeout(() => {
            this.getInfoDriver()
      }, 4000)
    }
}
</script>