<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between"> General </b-col>
          <b-col cols="1">
            <div class="float-right">
              <b-button-group>
                <b-btn href="javascript:void(0)" v-b-toggle="'accordion-general'" variant="link">
                  <span class="collapse-icon"></span>
                </b-btn>
              </b-button-group>
            </div>
          </b-col>
        </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-general`" visible :accordion="`accordion-master`">
        <b-card-body>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.number_questions_show" label="Número de preguntas a evaluar" type="number" name="number_questions_show" min="1" :error="form.errorsFor('number_questions_show')" placeholder="Número de preguntas a evaluar"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.number_attemps" label="Número de intentos" min="1" type="number" name="number_attemps" :error="form.errorsFor('number_attemps')" placeholder="Número de intentos"></vue-input>            
            <!--<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.max_calification" min="1" label="Calificación máxima" type="number" name="max_calification" :error="form.errorsFor('max_calification')" placeholder="Calificación máxima"></vue-input>-->
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.min_calification" label="Número de preguntas mínimas para aprobar" type="number" name="min_calification" min="1" :error="form.errorsFor('min_calification')" placeholder="Calificación minima  para aprobar"></vue-input>
          </b-form-row>
          <b-form-row>            
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.activity_id" :error="form.errorsFor('activity_id')" :selected-object="form.multiselect_activity" :multiple="true" :allowEmpty="true" name="activity_id" label="Actividades" placeholder="Seleccione las actividades relacionadas" :url="activitiesUrl">
            </vue-ajax-advanced-select>
            <!--<vue-file-simple :disabled="viewOnly" :help-text="form.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/trainingContract/download/${form.id}' target='blank'>aqui</a> ` : null" class="col-md-6" v-model="form.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`file`)" :maxFileSize="20"/>-->
          </b-form-row>
        </b-card-body>
      </b-collapse>
    </b-card>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between"> Material de apoyo </b-col>
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
      <b-collapse :id="`accordion-file`" visible :accordion="`accordion-master`">
      <b-card-body>
        <template v-for="(file, index) in form.files">
          <div :key="file.key">
              <b-form-row>
                <div class="col-md-12">
                    <div class="float-right">
                        <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeFile(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                    </div>
                </div>
                <vue-input class="col-md-6" v-model="file.name" label="Nombre" name="name" type="text" placeholder="Nombre" :error="form.errorsFor(`files.${index}.name`)"></vue-input>
                <vue-radio :disabled="viewOnly" class="col-md-6" v-model="file.type" :options="fileLink" :name="`fileLink${index}`" :error="form.errorsFor(`file.${index}.type`)" label="Elige el tipo de material" :checked="file.type">
                    </vue-radio>
                <vue-input v-if="(file.type == 'Archivo' && viewOnly) || (file.type == 'Archivo' && isEdit)" :disabled="true" class="col-md-4" v-model="file.type_file" label="Tipo de archivo" name="type_file" type="text" placeholder="Tipo de archivo" :error="form.errorsFor(`files.${index}.type_file`)"></vue-input>
                <vue-file-simple v-if="file.type == 'Archivo'" :disabled="viewOnly" :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/trainingContract/download/${file.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`file`)" :maxFileSize="20"/>

                <vue-input v-if="file.type == 'Link'" class="col-md-12" v-model="file.link" label="Link (Debe agregar el prefijo https://)" name="link" type="text" placeholder="Link" :error="form.errorsFor(`files.${index}.link`)"></vue-input>
              </b-form-row>
          </div>
        </template>

        <b-form-row style="padding-bottom: 20px;">
          <div class="col-md-12">
              <center><b-btn v-if="!viewOnly" variant="primary" @click.prevent="addFile()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn></center>
          </div>
        </b-form-row>          
      </b-card-body>
      </b-collapse>
    </b-card>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
          <b-row>
            <b-col cols="11" class="d-flex justify-content-between"> Preguntas </b-col>
            <b-col cols="1">
                <div class="float-right">
                  <b-button-group>
                    <b-btn href="javascript:void(0)" v-b-toggle="'accordion-questions'" variant="link">
                      <span class="collapse-icon"></span>
                    </b-btn>
                  </b-button-group>
                </div>
            </b-col>
          </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-questions`" visible :accordion="`accordion-master`">
        <b-card-body>
          <div class="col-md-12">
            <blockquote class="blockquote text-center">
              <p class="mb-0">Preguntas de la capacitación</p>
            </blockquote>
            <b-form-row>
              <b-form-feedback class="d-block" v-if="form.errorsFor(`questions`)" style="padding-bottom: 10px;">
                {{ form.errorsFor(`questions`) }}
              </b-form-feedback>
              <div class="col-md-12" v-if="!viewOnly">
                <div class="float-right" style="padding-top: 10px; padding-bottom: 10px">
                  <b-btn variant="primary" @click.prevent="addQuestion()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar pregunta</b-btn>
                </div>
              </div>
            </b-form-row>

            <template v-for="(question, index) in form.questions">
              <b-card no-body class="mb-2 border-secondary" :key="question.key" style="width: 100%;">
                <b-card-header class="bg-secondary">
                  <b-row>
                      <b-col cols="10" class="d-flex justify-content-between"> <strong>{{ question.description ? (question.description.length > 200 ? `${question.description.substring(0, 200)}...` : question.description) : `Nueva Pregunta ${index + 1}` }}</strong>  </b-col>
                      <b-col cols="2">
                        <div class="float-right">
                          <b-button-group>
                            <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + question.key+'-1'" variant="link">
                              <span class="collapse-icon"></span>
                            </b-btn>
                            <b-btn @click.prevent="removeQuestion(index)" 
                              v-if="!viewOnly"
                              size="sm" 
                              variant="secondary icon-btn borderless"
                              v-b-tooltip.top title="Eliminar Pregunta">
                                <span class="ion ion-md-close-circle"></span>
                            </b-btn>
                          </b-button-group>
                        </div>
                      </b-col>
                  </b-row>
                </b-card-header>
                <b-collapse :id="`accordion${question.key}-1`" :visible="!isEdit && !viewOnly" :accordion="`accordion-123`">
                  <b-card-body>
                    <b-form-row>
                      <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="question.description" label="Enunciado" name="description" placeholder="Descripción" :error="form.errorsFor(`questions.${index}.description`)" rows="3"></vue-textarea>
                    </b-form-row>
                    <b-form-row>
                      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="question.type_question_id" :error="form.errorsFor(`questions.${index}.type_question_id`)" :selected-object="question.multiselect_type_question_id" :multiple="false" name="type_question_id" label="Tipo de pregunta" placeholder="Seleccione el tipo de pregunta" :url="typeQuestionUrl">
                      </vue-ajax-advanced-select>
                    </b-form-row>
                    <b-form-row>
                      <!--Selección simple-->
                      <vue-textarea v-if="question.type_question_id == '1'" :disabled="viewOnly" class="col-md-12" v-model="question.options" :error="form.errorsFor(`questions.${index}.options`)" label="Opciones de respuestas (Separadas por enter)" name="options" placeholder="Opciones de respuestas" rows="3"></vue-textarea>
                      <vue-input v-if="question.type_question_id == '1'" :disabled="viewOnly" class="col-md-12" v-model="question.answers" label="Respuesta valida (Debe estar dentro de las opciones dadas en el campo anterior)" type="text" name="answers" :error="form.errorsFor(`questions.${index}.answers`)" placeholder="Respuesta valida"></vue-input>

                      <!--Verdadero o falso-->
                      <vue-radio v-if="question.type_question_id == '2'" :disabled="viewOnly" class="col-md-12" v-model="question.answers" :options="trueFalse" :name="`trueFalse${index}`" :error="form.errorsFor(`questions.${index}.answers`)" label="Elige la opciòn correcta" :checked="question.answers">
                      </vue-radio>

                      <!--Selección multiple-->
                      <vue-textarea v-if="question.type_question_id == '3'" :disabled="viewOnly" class="col-md-12" v-model="question.options" label="Opciones de respuestas (Separadas por enter)" name="options" placeholder="Opciones de respuestas" rows="3" :error="form.errorsFor(`questions.${index}.options`)"></vue-textarea>
                      <vue-textarea v-if="question.type_question_id == '3'" :disabled="viewOnly" class="col-md-12" v-model="question.answers" label="Respuestas validas (Separadas por enter. Deben estar dentro de las opciones dadas en el campo anterior)" name="answers" placeholder="Respuestas validas" rows="3" :error="form.errorsFor(`questions.${index}.answers`)"></vue-textarea>

                      <!--Si o NO-->
                      <vue-radio v-if="question.type_question_id == '4'" :disabled="viewOnly" class="col-md-12" v-model="question.answers" :options="siNo" :name="`siNo${index}`" :error="form.errorsFor(`questions.${index}.answers`)" label="Elige la opciòn correcta" :checked="question.answers">
                      </vue-radio>

                      <!--Emparejamiento-->
                      <vue-textarea v-if="question.type_question_id == '5'" :disabled="viewOnly" class="col-md-12" v-model="question.options" label="Opciones (Separadas por enter)" name="options" placeholder="Opciones" rows="3" :error="form.errorsFor(`questions.${index}.options`)"></vue-textarea>
                      <vue-textarea v-if="question.type_question_id == '5'" :disabled="viewOnly" class="col-md-12" v-model="question.answers" label="Respuestas (Separadas por enter. Deben estar en el mismo orden de las opciones)" name="answers" placeholder="Respuestas" rows="3" :error="form.errorsFor(`questions.${index}.answers`)"></vue-textarea>
                      
                    </b-form-row>
                    

                    <!--<b-form-row>
                      <vue-input :disabled="viewOnly" class="col-md-12" v-model="question.value_question" label="Valor de la pregunta" type="number" name="value_question" min="1" :error="form.errorsFor(`questions.${index}.value_question`)" placeholder="Valor de la pregunta"></vue-input>
                    </b-form-row>-->
                  </b-card-body>
                </b-collapse>
              </b-card>
            </template>
          </div>
        </b-card-body>
      </b-collapse>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Alerts from '@/utils/Alerts.js';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    VueCheckboxSimple,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },   
    activitiesUrl: { type: String, default: "" }, 
    typeQuestionUrl: { type: String, default: "" },
    training: {
      default() {
        return {
          name: '',
          number_questions_show: '',
          number_attemps: '',
          min_calification: '',
          files: [],         
          activity_id: [],
          questions: [],
          type_pairing: [],
          delete: {
            files: [],
            questions: []
          }
        };
      }
    }
  },
  watch: {
    training() {
      this.loading = false;
      this.form = Form.makeFrom(this.training, this.method);
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.training, this.method),
        siNo: [
          {text: 'SI', value: 'SI'},
          {text: 'NO', value: 'NO'}
        ],
        trueFalse: [
          {text: 'Verdadero', value: 1},
          {text: 'Falso', value: 0}
        ],
        fileLink: [
          {text: 'Archivo', value: 'Archivo'},
          {text: 'Link', value: 'Link'}
        ]
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

      this.form.clearFilesBinary();
        _.forIn(this.form.files, (file, keyFile) => {
          if (file.file)
            this.form.addFileBinary(`${keyFile}`, file.file);
        });

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-contracts-trainings-virtual" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addFile() 
    {
      this.form.files.push({
          key: new Date().getTime(),
          name: '',
          type: '',
          file: '',
          link: ''
      })
    },
	  removeFile(index)
    {
      if (this.form.files[index].id != undefined)
        this.form.delete.files.push(this.form.files[index].id)

      this.form.files.splice(index, 1)
    },
    addQuestion() {
        this.form.questions.push({
            key: new Date().getTime(),
            description: '',
            type_question_id: [],
            
            value_question: ''
        })
    },
    removeQuestion(index)
    {
      if (this.form.questions[index].id != undefined)
        this.form.delete.questions.push(this.form.questions[index].id)

      this.form.questions.splice(index, 1)
    }
  }
}
</script>
