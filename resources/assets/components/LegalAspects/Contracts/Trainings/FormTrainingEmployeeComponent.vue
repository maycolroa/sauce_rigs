<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="REALIZAR CAPACITACIÓN"
    />

    <div class="col-md-10 offset-1" v-if="!error && !result">
      <b-card no-body>
        <b-card-body>
            <b-form :action="action" @submit.prevent="submit" autocomplete="off">

                <b-card bg-variant="transparent" border-variant="dark" :title="form.name" class="mb-3 box-shadow-none">
                    <b-row>
                        <b-col>
                            <div><b>Número de intentos permitidos:</b> {{ form.number_attemps }}</div>
                        </b-col>
                        <b-col>
                            <div><b>Número de intento:</b> {{ form.attempt }}</div>
                        </b-col>
                        <!--<b-col v-if="training.file">
                          <div class="text-center"><b>Descarga aqui el material de apoyo para la resolución de la capacitación</b></div>
                          <center><a class="btn btn-primary" :href="`/training/download/file/${form.id}`" target='blank'><i class="fas fa-download"></i></a></center>
                        </b-col>-->
                    </b-row>
                </b-card>

                <b-card v-show="form.files" bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                  <div class="text-center"><b>Descarga aqui el material de apoyo para la resolución de la capacitación</b></div>
                  <template v-for="(file, index) in form.files">
                     <div :key="file.key">
                      <center>
                        <b-row>                       
                          <p>{{ `${index + 1}.`}} </p><a :href="`/training/download/file/${file.id}`" target='blank'>{{ file.name }}</a>
                          <br>
                        </b-row>
                      </center>
                     </div>
                  </template>
                </b-card>

                <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                    <blockquote class="blockquote text-center">
                        <p class="mb-0">Preguntas</p>
                    </blockquote>

                    <template v-for="(question, index) in form.questions">
                      <div :key="question.key">
                        <b-form-row>
                            <p>{{ `${index + 1}. ${question.description} `}}</p>
                        </b-form-row>

                        <b-form-row>
                            <!--Seleccion simple-->
                            <vue-advanced-select v-if="question.type.name == 'selection_simple'" class="col-md-12" v-model="question.answers" name="answers" label="Respuesta" placeholder="Seleccione la respuesta" :options="question.answer_options.options">
                            </vue-advanced-select>

                            <!--Seleccion multiple-->
                            <vue-advanced-select v-if="question.type.name == 'selection_multiple'" limit="50" class="col-md-12" v-model="question.answers" name="answers" label="Respuesta" :multiple="true" placeholder="Seleccione las respuestas" :options="question.answer_options.options">
                            </vue-advanced-select>

                            <!--verdadero y falso-->
                            <vue-radio v-if="question.type.name == 'true_false'" class="col-md-12" v-model="question.answers" :options="trueFalse" :name="`trueFalse${index}`" label="Respuesta"></vue-radio>

                            <!--Si y no-->
                            <vue-radio v-if="question.type.name == 'yes_no'" class="col-md-12" v-model="question.answers" :options="siNo" :name="`siNo${index}`" label="Respuesta"></vue-radio>
                          
                        </b-form-row>
                      </div>
                    </template>
                </b-card>

                <b-modal ref="modalConfirm" :hideFooter="true" id="modals-historial2" class="modal-top" size="lg">
                    <div slot="modal-title">
                        <h4>Confirmación</h4>
                    </div>
                    <center>
                        <p> ¿Esta seguro de que desea continuar con el envio de la capacitación? </p>
                    </center>

                    <div class="row float-right pt-12 pr-12y">                      
                        <b-btn type="submit" :disabled="loading" variant="primary">SI</b-btn>&nbsp;&nbsp;
                        <b-btn variant="primary" @click="$refs.modalConfirm.hide()">NO</b-btn>
                    </div>
                </b-modal>

                 <div class="row float-right pt-10 pr-10" style="padding-top: 20px; padding-right: 25px;">
                  <template>
                    <b-btn @click="$refs.modalConfirm.show()" :disabled="loading" variant="primary">Finalizar</b-btn>
                  </template>
                </div>
            </b-form>
        </b-card-body>
      </b-card>
    </div>
    <div class="col-md-10 offset-1" v-if="error">
      <b-card no-body>
        <b-card-body>
          <center><h3>{{ error }}</h3></center>
        </b-card-body>
      </b-card>
    </div>

    <div class="col-md-10 offset-1" v-if="result">
        <b-card no-body>
          <b-card-body>
            <center><h3>Usted ha {{ result }} la capacitación</h3></center>
          </b-card-body>
        </b-card>
    </div>
  </div>
</template>

<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import Loading from "@/components/Inputs/Loading.vue";

export default {
  components: {
    VueAdvancedSelect,
    VueRadio,
    PerfectScrollbar,
    Loading
  },
  props: {
    action: { type: String },
    method: { type: String },
    error: { type: String },
    training: {
      default() {
        return {
            id: '', 
            name: '',
            files: {},
            number_attemps: '',
            attempt: '',
            employee: '',
            questions: {}
        };
      }
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
        result: ''
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.$refs.modalConfirm.hide()
                        
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.result = response.data.result;
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
