<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.identification" label="Identificación" type="text" name="identification" :error="form.errorsFor('identification')" placeholder="Identificación"/>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"/>
    </b-form-row>

    <b-form-row>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date_of_birth" label="Fecha de nacimiento" :full-month-name="true" placeholder="Seleccione la fecha de nacimiento" :error="form.errorsFor('date_of_birth')" name="date_of_birth" :disabled-dates="disabledDatesBirth">
          </vue-datepicker>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.sex" :error="form.errorsFor('sex')" :multiple="false" :options="sexs" :hide-selected="false" name="sex" label="Sexo" placeholder="Seleccione el sexo">
          </vue-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.phone_residence" label="Teléfono de residencia" type="text" name="phone_residence" :error="form.errorsFor('phone_residence')" placeholder="Teléfono de residencia"/>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.phone_movil" label="Teléfono de movil" type="text" name="phone_movil" :error="form.errorsFor('phone_movil')" placeholder="Teléfono de movil"/>
    </b-form-row>

    <b-form-row>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.civil_status" :error="form.errorsFor('civil_status')" :multiple="false" :options="stateCivilOption" :hide-selected="false" name="civil_status" label="Estado civil" placeholder="Seleccione el estado civil">
          </vue-advanced-select>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.direction" label="Dirección" type="text" name="direction" :error="form.errorsFor('direction')" placeholder="Dirección"/>
    </b-form-row>


    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"/>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.workday" :error="form.errorsFor('workday')" :multiple="false" :options="workdayOption" :hide-selected="false" name="workday" label="Jornada laboral" placeholder="Seleccione la Jornada laboral">
          </vue-advanced-select>
    </b-form-row>

    <b-form-row>
    <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.departament_id" :selected-object="form.multiselect_departament" name="departament_id" :error="form.errorsFor('departament_id')" label="Departamento" placeholder="Seleccione el departamento" :url="departamentsUrl"></vue-ajax-advanced-select>
    <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.city_id" :selected-object="form.multiselect_city" name="city_id" :error="form.errorsFor('city_id')" label="Municipio" placeholder="Seleccione el municipio" :url="minicipalitiessUrl" :parameters="{departament: form.departament_id }"></vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.income_date" label="Fecha de ingreso" :full-month-name="true" placeholder="Seleccione la fecha de ingreso" :error="form.errorsFor('income_date')" name="income_date">
          </vue-datepicker>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.position" label="Cargo" type="text" name="position" :error="form.errorsFor('position')" placeholder="Cargo"/>
    </b-form-row>

    <b-form-row >
      <vue-radio :disabled="viewOnly" class="col-md-6" v-model="form.disability_condition" :options="siNo" name="disability_condition" label="Condición de discapacidad" :checked="form.disability_condition" :error="form.errorsFor('disability_condition')"></vue-radio>
      <vue-textarea v-if="form.disability_condition == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="form.disability_description" label="Descripción condición de discapacidad" name="disability_description" placeholder="Descripción" rows="3" :error="form.errorsFor('disability_description')"></vue-textarea>
    </b-form-row>

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.emergency_contact" label="Contacto de emergencia" type="text" name="emergency_contact" :error="form.errorsFor('emergency_contact')" placeholder="Contacto de emergencia"/>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.emergency_contact_phone" label="Telefono contacto de emergencia" type="text" name="emergency_contact_phone" :error="form.errorsFor('emergency_contact_phone')" placeholder="Telefono contacto de emergencia"/>
    </b-form-row>

    <b-form-row>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.rh" :error="form.errorsFor('rh')" :multiple="false" :options="rhOptions" :hide-selected="false" name="rh" label="Tipo de Sangre" placeholder="Seleccione el tipo">
          </vue-advanced-select>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.salary" label="Salario" type="number" name="salary" :error="form.errorsFor('salary')" placeholder="Salario"/>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_afp_id" :error="form.errorsFor('employee_afp_id')" :selected-object="form.multiselect_afp" name="employee_afp_id" label="AFP" placeholder="Seleccione una opción" :url="afpDataUrl">
      </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_eps_id" :error="form.errorsFor('employee_eps_id')" :selected-object="form.multiselect_eps" name="employee_eps_id" :label="keywordCheck('eps')" placeholder="Seleccione una opción" :url="epsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row v-if="auth.proyectContract == 'SI'">
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.proyects_id" :error="form.errorsFor('proyects_id')" :selected-object="form.multiselect_proyect" :multiple="true" :allowEmpty="true" name="proyects_id" label="Proyectos" placeholder="Seleccione las proyectos a asignar" :url="proyectsUrl">
      </vue-ajax-advanced-select>
    </b-form-row>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%; margin-top:15px; margin-bottom: 15px;">
      <b-card-header class="bg-secondary">
        <b-row>
            <b-col cols="10" class="d-flex justify-content-between"> <strong>Observaciones</strong>  </b-col>
            <b-col cols="2">
              <div class="float-right">
                <b-button-group>
                  <b-btn href="javascript:void(0)" v-b-toggle="'accordion-observations'" variant="link">
                    <span class="collapse-icon"></span>
                  </b-btn>
                </b-button-group>
              </div>
          </b-col>
        </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-observations`" :visible="true" :accordion="`accordion-123`">
        <b-card-body>
          <b-form-row>
            <div class="col-md-12">
              <employee-observations
                :disabled="true"
                :editable-observations="true"
                :old-observations="form.old_observations"
                :employee-id="form.id"
                :user-contract="true"
                ref="observationInserter"
              >
              </employee-observations>
            </div>
          </b-form-row></b-card-body>
      </b-collapse>
    </b-card>

    <br><br>

    <div class="col-md-12" style="margin-top:15px; margin-bottom: 15px;">
      <blockquote class="blockquote text-center">
          <p class="mb-0">Actividades</p>
      </blockquote>
      <b-form-row>
        <div class="col-md-12" v-if="!viewOnly">
          <div class="float-right" style="padding-top: 10px; padding-bottom: 10px">
            <b-btn variant="primary" @click.prevent="addActvity()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Actividad</b-btn>
          </div>
        </div>
        <b-form-feedback class="d-block" v-if="form.errorsFor(`activities`)" style="padding-bottom: 10px;">
          {{ form.errorsFor(`activities`) }}
        </b-form-feedback>
      </b-form-row>

      <template v-for="(activity, index) in form.activities">
        <b-card no-body class="mb-2 border-secondary" :key="activity.key" style="width: 100%;">
          <b-card-header class="bg-secondary">
            <b-row>
                <b-col cols="10" class="d-flex justify-content-between"> <strong>{{ activity.name ? activity.name : `Nueva Actividad ${index + 1}` }}</strong>  </b-col>
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
          <b-collapse :id="`accordion${activity.key}-1`" :visible="!isEdit && !viewOnly" :accordion="`accordion-123`">
            <b-card-body>
              <b-form-row>
                <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="activity.selected" :error="form.errorsFor(`activities.${index}.selected`)" :selected-object="activity.multiselect_activity" :multiple="false" name="activity" label="Actividad" placeholder="Seleccione la actividad" :url="activitiesUrl" @selectedName="updateActivityNameTab($event, index)" @input="searchFiles(activity)">
                </vue-ajax-advanced-select>
              </b-form-row>

              <blockquote class="blockquote text-center" v-if="activity.documents.length > 0"><p class="mb-0">Documentos Requeridos</p></blockquote>

              <b-card bg-variant="transparent" border-variant="dark" :title="document.name" class="mb-3 box-shadow-none" v-for="(document, indexDocument) in activity.documents" :key="document.key">
                <b-row>
                  <b-col>
                    <div class="col-md-12">
                      <b-form-row>
                        <vue-radio :disabled="viewOnly" :checked="document.apply_file" class="col-md-12" v-model="document.apply_file" :options="siNo" :name="`apply_file${document.id}`" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.apply_file`)" label="¿Aplica el documento para este empleado?">
                        </vue-radio>
                        <div class="col-md-12" v-if="!viewOnly && document.apply_file == 'SI'">
                          <div class="float-right" style="padding-top: 10px;">
                            <b-btn variant="primary" @click.prevent="addFile(document)"><span class="ion ion-md-add-circle"></span> Añadir archivo</b-btn>
                          </div>
                        </div>
                      </b-form-row>
                      <b-form-row v-if="document.apply_file == 'SI'" style="padding-top: 15px;">
                        <template v-for="(file, indexFile) in document.files">
                          <b-card no-body class="mb-2 border-secondary" :key="file.key" style="width: 100%;" >
                            <b-card-header class="bg-secondary">
                              <b-row>
                                <b-col cols="10" class="d-flex justify-content-between"> Archivo #{{ indexFile + 1 }}</b-col>
                                <b-col cols="2">
                                  <div class="float-right">
                                    <b-button-group>
                                      <b-btn href="javascript:void(0)" v-b-toggle="'accordion'+ file.key +'-1'" variant="link">
                                        <span class="collapse-icon"></span>
                                      </b-btn>
                                      <b-btn @click.prevent="removeFile(document, indexFile)"
                                        v-if="(auth.deleteFilesAproverContracts == 'SI' && isEdit && !viewOnly) || (auth.deleteFilesAproverContracts == 'NO' && file.state == 'PENDIENTE' && isEdit && !viewOnly) || !file.id"
                                        size="sm" 
                                        variant="secondary icon-btn borderless"
                                        v-b-tooltip.top title="Eliminar Archivo">
                                        <span class="ion ion-md-close-circle"></span>
                                      </b-btn>
                                    </b-button-group>
                                  </div>
                                </b-col>
                              </b-row>
                            </b-card-header>
                            <b-collapse :id="`accordion${file.key}-1`" visible :accordion="`accordion-activities.${index}.documents.${indexDocument}`">
                              <b-card-body border-variant="primary" class="mb-3 box-shadow-none">
                                <div class="rounded ui-bordered p-3 mb-3">
                                  <div v-if="file.apply_file == 'SI'">
                                    <b-form-row>
                                      <vue-input :disabled="viewOnly" class="col-md-6" v-model="file.name" label="Nombre" type="text" name="name"  placeholder="Nombre" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.name`)" help-text="El nombre no debe contener caracteres especiales como '/', '.'"/>
                                      <vue-radio :disabled="viewOnly || file.required_date" class="col-md-6" v-model="file.required_expiration_date" :options="siNo" :name="`siNo${indexDocument}${indexFile}`" label="Requiere fecha de vencimiento" :checked="file.required_expiration_date">
                                      </vue-radio>
                                    </b-form-row>
                                    <b-form-row  v-if="file.required_expiration_date == 'SI'">
                                      <vue-datepicker v-if="auth.company_id == 712" :disabled="viewOnly" class="col-md-6" v-model="file.expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento"  name="expirationDate" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.expirationDate`)"/>
                                      <vue-datepicker v-else :disabled="viewOnly" class="col-md-6" v-model="file.expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento"  name="expirationDate" :disabled-dates="disabledDates" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.expirationDate`)"/>
                                    </b-form-row>

                                    <b-form-row>
                                      <vue-file-simple :disabled="!file.edit_document || viewOnly" :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/fileUpload/download/${file.id}' target='blank'>aqui</a> ` : 'El tamaño del archivo no debe ser mayor a 15MB.'" class="col-md-12" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.file`)" :maxFileSize="20"/>
                                    </b-form-row>
                                  </div>
                                  <div v-if="file.apply_file == 'NO'">
                                    <b-form-row>
                                      <vue-textarea class="col-md-12" v-model="file.apply_motive" label="Motivo por el cual no aplica" name="apply_motive" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.apply_motive`)" placeholder="Motivo por el cual no aplica" :disabled="viewOnly"></vue-textarea>
                                    </b-form-row>
                                  </div>
                                  <b-form-row>
                                    <vue-advanced-select v-if="file.id" class="col-md-6" v-model="file.state" :disabled="true" name="state" label="Estado del documento" placeholder="Seleccione el estado" :options="states" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.state`)" :multiple="false" :allow-empty="false">
                                    </vue-advanced-select>
                                    <vue-textarea v-if="file.state == 'RECHAZADO'" class="col-md-6" v-model="file.reason_rejection" label="Motivo del rechazo" name="reason_rejection" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.reason_rejection`)" placeholder="Motivo del rechazo" :disabled="true"></vue-textarea>
                                  </b-form-row>
                                  
                                </div>
                              </b-card-body>
                            </b-collapse>
                          </b-card>
                        </template>
                      </b-form-row>
                      <b-form-row v-if="document.apply_file == 'NO'">
						            <vue-textarea class="col-md-12" v-model="document.apply_motive" label="Motivo por el cual no aplica" name="apply_motive" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.apply_motive`)" placeholder="Motivo por el cual no aplica" :disabled="viewOnly"></vue-textarea>
                      </b-form-row>
                      <b-form-row v-if="document.apply_file == 'NO'">
                        <vue-advanced-select class="col-md-6" v-model="document.state_file" :disabled="true" name="state_file" label="Estado del documento" placeholder="Seleccione el estado" :options="states" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.state_file`)" :multiple="false" :allow-empty="false">
                        </vue-advanced-select>
                        <vue-textarea v-if="document.state == 'RECHAZADO'" class="col-md-6" v-model="document.motive_rejected_file" label="Motivo del rechazo" name="motive_rejected_file" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.motive_rejected_file`)" placeholder="Motivo del rechazo" :disabled="true"></vue-textarea>
                      </b-form-row>
                    </div>
                  </b-col>
                </b-row>
            </b-card>
            </b-card-body>
          </b-collapse>
        </b-card>
      </template>
    </div>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import GlobalMethods from '@/utils/GlobalMethods.js';
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Alerts from '@/utils/Alerts.js';
import EmployeeObservations from "./EmployeeObservations.vue";

export default {
  components: {
    VueFileSimple,
    VueAjaxAdvancedSelect,
    VueInput,
    VueDatepicker,
    PerfectScrollbar,
    VueRadio,
    VueAdvancedSelect,
    VueTextarea,
    EmployeeObservations
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },    
    activitiesUrl: { type: String, default: "" },
    afpDataUrl: { type: String, default: "" },
    sexs: {
      type: Array,
      default: function() {
        return [];
      }
    },	
    states: {
      type: Array,
      default: function() {
        return [];
      }
    },
    employee: {
      default() {
        return {
            name: '',
            identification: '',
            position: '',
            email: '',
            employee_afp_id: '',    
            employee_eps_id: '',  
            sex:'',    
            phone_residence: '',
            phone_movil: '',
            direction: '',
            disability_condition: '',
            emergency_contact: '',
            rh:'',
            salary: '',
            date_of_birth: '',
            activities: [],
            delete: {
              files: []
            },
            disability_description: '',
            emergency_contact_phone: '',
            proyects_id: [],
            departament_id: '',
            city_id: '',
            income_date: '',
            new_observations: [],
            old_observations: []
        };
      }
    }
  },
  watch: {
    employee() {
      this.form = Form.makeFrom(this.employee, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.employee, this.method),
      disabledDates: {
        to: new Date()
      },
      disabledDatesBirth: {
        from: new Date()
      },
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
      epsDataUrl: '/selects/eps',
			proyectsUrl: '/selects/contracts/ctProyectsContracts',
      departamentsUrl: '/selects/departaments',
      minicipalitiessUrl: '/selects/municipalities',
      rhOptions: [
          {name: 'A', value: 'A'},
          {name: 'B', value: 'B'},
          {name: 'O', value: 'O'},
          {name: 'AB', value: 'AB'},
          {name: 'A+', value: 'A+'},
          {name: 'A-', value: 'A-'},
          {name: 'B+', value: 'B+'},
          {name: 'B-', value: 'B-'},
          {name: 'O+', value: 'O+'},
          {name: 'O-', value: 'O-'},
          {name: 'AB+', value: 'AB+'},
          {name: 'AB-', value: 'AB-'},
        ],
        stateCivilOption: [
          {name: 'Soltero', value: 'Soltero'},
          {name: 'Casado', value: 'Casado'},
        ],
        workdayOption: [
          {name: 'Jornada Normal', value: 'Jornada Normal'},
          {name: 'Jornada Nocturna', value: 'Jornada Nocturna'},
          {name: 'Jornada Mixta', value: 'Jornada Mixta'},
          {name: 'Jornada Diurna', value: 'Jornada Diurna'},
        ],
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

      this.form.clearFilesBinary();
      _.forIn(this.form.activities, (activity, keyActvity) => {
        _.forIn(activity.documents, (documento, keyDocument) => {
          _.forIn(documento.files, (file, keyFile) => {
              if (file.file)
                this.form.addFileBinary(`${keyActvity}_${keyDocument}_${keyFile}`, file.file);
              else
              {
                if (documento.apply_file == 'NO')
                {
                  if (!file.id)
                    documento.files.splice(keyFile, 1)
                }
              }
            
            });
        });
      });

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: 'legalaspects-contracts-employees' });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addActvity() {
        this.form.activities.push({
            key: new Date().getTime() + Math.round(Math.random() * 10000),
            name: '',
            selected: '',
            documents: []
        })
    },
    removeActivity(index)
    {
      this.form.activities.splice(index, 1)
    },
    updateActivityNameTab(values, index) {
      this.form.activities[index].name = values
    },
    searchFiles(activity)
    {
      if (activity.selected)
      {
        axios.post('/legalAspects/employeeContract/files',{
          activity: activity.selected,
          employee: this.form.id,
          contract_id: this.form.contract_id
        })
        .then(response => {
            activity.documents = response.data.data;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
      }
    },
    /*addFile(documento) {
      documento.files.push({
        key: new Date().getTime(),
        name: '',
        expirationDate: '',
        file: ''
      });
    },*/
    addFile(documento) {
			let required = false;

			if (documento.required_expired_date === 'SI')
			{
				required = true;
			}

      let document_name = documento.name.replace('.', '')
      document_name = document_name.replace('/', '-')
				
			let content = {
				key: new Date().getTime(),
				name: document_name,
				expirationDate: '',
				file: '',
				required_expiration_date: 'SI',
				required_date: required,
        apply_file: 'SI',
        edit_document: true
			}

	      documento.files.push(content);
	    },
    removeFile(documento, index) {
      if (documento.files[index].id != undefined)
        this.form.delete.files.push(documento.files[index].id)
        
      documento.files.splice(index, 1);
    }
  }
};
</script>
