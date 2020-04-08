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
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.max_calification" min="1" label="Calificación máxima" type="number" name="max_calification" :error="form.errorsFor('max_calification')" placeholder="Calificación máxima"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.min_calification" label="Calificación mínima para aprobar" type="number" name="min_calification" min="1" :error="form.errorsFor('min_calification')" placeholder="Calificación minima  para aprobar"></vue-input>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.activity_id" :error="form.errorsFor('activity_id')" :selected-object="form.multiselect_activity" :multiple="true" :allowEmpty="true" name="activity_id" label="Actividades" placeholder="Seleccione las actividades relacionadas" :url="activitiesUrl">
            </vue-ajax-advanced-select>
          </b-form-row>
          <b-form-row>
            <vue-file-simple :disabled="viewOnly" :help-text="form.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/trainingContract/download/${form.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="form.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`file`)"/>
          </b-form-row>
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
    typesEvaluation: {
      type: Array,
      default: function() {
        return [];
      }
    },
    training: {
      default() {
        return {
          name: '',
          number_questions_show: '',
          number_attemps: '',
          max_calification: '',
          min_calification: '',
          file: '',         
          activity_id: [],
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
        form: Form.makeFrom(this.training, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-contracts-trainings" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addObjetive() {
        this.form.objectives.push({
            key: new Date().getTime(),
            description: '',
            subobjectives: []
        })
    },
    removeObjetive(index)
    {
      if (this.form.objectives[index].id != undefined)
        this.form.delete.objectives.push(this.form.objectives[index].id)

      this.form.objectives.splice(index, 1)
    }
  }
}
</script>
