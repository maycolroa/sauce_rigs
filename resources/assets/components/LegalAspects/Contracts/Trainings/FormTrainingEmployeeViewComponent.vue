<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between"> General </b-col>
        </b-row>
      </b-card-header>
      <b-card-body>
        <b-form-row>
          <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
          <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.attempt" label="Número de intentos" min="1" type="number" name="attempt" :error="form.errorsFor('attempt')" placeholder="Número de intentos"></vue-input> 
        </b-form-row>
        <b-form-row>
          <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.qualification" label="Calificación" type="text" name="qualification" min="1" :error="form.errorsFor('qualification')" placeholder="Calificación"></vue-input>
        </b-form-row>
      </b-card-body>
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
      <b-card-body>
        <div class="col-md-12">
          <blockquote class="blockquote text-center">
            <p class="mb-0">Preguntas de la capacitación</p>
          </blockquote>

          <template v-for="(question, index) in form.questions">
            <b-card no-body class="mb-2 border-secondary" :key="question.key" style="width: 100%;">
              <b-card-body>
                <b-form-row>
                  <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="question.description" label="Enunciado" name="description" placeholder="Descripción" :error="form.errorsFor(`questions.${index}.description`)" rows="3"></vue-textarea>
                </b-form-row>
                <b-form-row>
                  <!--Selección simple-->
                  <vue-input v-if="question.type_question_id == '1'" :disabled="viewOnly" class="col-md-12" v-model="question.answer" label="Respuesta" type="text" name="answer" :error="form.errorsFor(`questions.${index}.answer`)" placeholder="Respuesta"></vue-input>

                  <!--Verdadero o falso-->
                  <vue-radio v-if="question.type_question_id == '2'" :disabled="viewOnly" class="col-md-12" v-model="question.answer" :options="trueFalse" :name="`answer${index}`" :error="form.errorsFor(`questions.${index}.answer`)" label="Respuesta" :checked="question.answer">
                  </vue-radio>

                  <!--Selección multiple-->
                  <vue-textarea v-if="question.type_question_id == '3'" :disabled="viewOnly" class="col-md-12" v-model="question.answer" label="Respuestas" name="answer" placeholder="Respuestas" rows="3" :error="form.errorsFor(`questions.${index}.answer`)"></vue-textarea>

                  <!--Si o NO-->
                  <vue-radio v-if="question.type_question_id == '4'" :disabled="viewOnly" class="col-md-12" v-model="question.answer" :options="siNo" :name="`answer${index}`" :error="form.errorsFor(`questions.${index}.answer`)" label="Respuesta" :checked="question.answer">
                  </vue-radio>

                  <!--Emparejamiento-->
                  <vue-textarea v-if="question.type_question_id == '5'" :disabled="viewOnly" class="col-md-12" v-model="question.answer" label="Respuesta" name="answer" placeholder="Respuesta" rows="3" :error="form.errorsFor(`questions.${index}.answer`)"></vue-textarea>                    
                </b-form-row>
              </b-card-body>
            </b-card>
          </template>
        </div>
      </b-card-body>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
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
          attempt: '',
          qualification: '',
          questions: []
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
        ]
    };
  },
  methods: {}
}
</script>
