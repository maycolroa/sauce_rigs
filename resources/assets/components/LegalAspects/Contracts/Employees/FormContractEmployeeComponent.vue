<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.identification" label="Identificación" type="text" name="identification" :error="form.errorsFor('identification')" placeholder="Identificación"/>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"/>
    </b-form-row>

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"/>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.position" label="Cargo" type="text" name="position" :error="form.errorsFor('position')" placeholder="Cargo"/>
    </b-form-row>
    <div class="col-md-12">
      <blockquote class="blockquote text-center">
          <p class="mb-0">Actividades</p>
      </blockquote>
      <b-form-row>
        <div class="col-md-12" v-if="!viewOnly">
          <div class="float-right" style="padding-top: 10px; padding-bottom: 10px">
            <b-btn variant="primary" @click.prevent="addActvity()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Actividad</b-btn>
          </div>
        </div>
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

              </b-form-row>
              <b-card bg-variant="transparent" border-variant="dark" :title="document.name" class="mb-3 box-shadow-none" v-for="(document, indexDocument) in activity.documents" :key="document.key">
                <b-row>
                  <b-col>
                    <div class="col-md-12">
                      <b-form-row>
                        <div class="col-md-12" v-if="!viewOnly">
                          <div class="float-right" style="padding-top: 10px;">
                            <b-btn variant="primary" @click.prevent="addFile(document)"><span class="ion ion-md-add-circle"></span> Añadir archivo</b-btn>
                          </div>
                        </div>
                      </b-form-row>
                      <b-form-row style="padding-top: 15px;">
                        <template v-for="(file, indexFile) in document.files">
                          <b-card no-body class="mb-2 border-secondary" :key="file.key" style="width: 100%;" >
                            <b-card-header class="bg-secondary">
                              <b-row>
                                <b-col cols="10" class="d-flex justify-content-between text-white"> Archivo #{{ indexFile + 1 }}</b-col>
                                <b-col cols="2">
                                  <div class="float-right">
                                    <b-button-group>
                                      <b-btn href="javascript:void(0)" v-b-toggle="'accordion'+ file.key +'-1'" variant="link">
                                        <span class="collapse-icon"></span>
                                      </b-btn>
                                      <b-btn @click.prevent="removeFile(document, indexFile)"
                                        v-if="!viewOnly"
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
                        
                                  <b-form-row>
                                    <vue-input :disabled="viewOnly" class="col-md-6" v-model="file.name" label="Nombre" type="text" name="name"  placeholder="Nombre" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.name`)"/>
                                    <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="file.expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento"  name="expirationDate" :disabled-dates="disabledDates"/>
                                  </b-form-row>

                                  <b-form-row>
                                    <vue-file-simple :disabled="viewOnly" :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/fileUpload/download/${file.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`activities.${index}.documents.${indexDocument}.files.${indexFile}.file`)" maxFileSize="20"/>
                                  </b-form-row>
                                  
                                </div>
                              </b-card-body>
                            </b-collapse>
                          </b-card>
                        </template>
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
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueFileSimple,
    VueAjaxAdvancedSelect,
    VueInput,
    VueDatepicker,
    PerfectScrollbar
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },    
    activitiesUrl: { type: String, default: "" },
    employee: {
      default() {
        return {
            name: '',
            identification: '',
            position: '',
            email: '',          
            activities: [],
            delete: {
              files: []
            }
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
      }
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
      /*if (this.form.activities[index].id != undefined)
      {
        _.forIn(this.form.activities[index].documents, (documento, key) => {
            this.removeFile(documento, key);
        });
      }*/

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
          employee: this.form.id
        })
        .then(response => {
            activity.documents = response.data.data;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
      }
    },
    addFile(documento) {
      documento.files.push({
        key: new Date().getTime(),
        name: '',
        expirationDate: '',
        file: ''
      });
    },
    removeFile(documento, index) {
      if (documento.files[index].id != undefined)
        this.form.delete.files.push(documento.files[index].id)
        
      documento.files.splice(index, 1);
    }
  }
};
</script>
