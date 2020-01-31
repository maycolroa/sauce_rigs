<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card bg-variant="transparent" border-variant="dark" title="Datos Personales" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.patient_identification" label="Identificación" type="text" name="patient_identification" :error="form.errorsFor('patient_identification')" placeholder="Identificación"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.deal" label="Negocio" type="text" name="deal" :error="form.errorsFor('deal')" placeholder="Negocio"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre y Apellido" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre y Apellido"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.regional" label="Planta" type="text" name="regional" :error="form.errorsFor('regional')" placeholder="Planta"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.sex" label="Sexo" type="text" name="sex" :error="form.errorsFor('sex')" placeholder="Sexo"></vue-input>            
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.antiquity" label="Antiguedad" type="text" name="antiquity" :error="form.errorsFor('antiquity')" placeholder="Antiguedad"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.age" label="Edad" type="text" name="age" :error="form.errorsFor('age')" placeholder="Edad"></vue-input>            
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.state" label="Estado" type="text" name="state" :error="form.errorsFor('state')" placeholder="Estado"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.position" :label="keywordCheck('position')" type="text" name="position" :error="form.errorsFor('position')" placeholder="Seleccione una opción"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.symptomatology" label="Sintomatología" type="text" name="symptomatology" :error="form.errorsFor('symptomatology')" placeholder="Sintomatología"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.area" label="Área" type="text" name="area" :error="form.errorsFor('area')" placeholder="Área"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.habits" label="Hábitos" type="text" name="habits" :error="form.errorsFor('habits')" placeholder="Hábitos"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.history_of_respiratory_pathologies" label="Antecedentes de patologias respiratorias" type="text" name="history_of_respiratory_pathologies" :error="form.errorsFor('history_of_respiratory_pathologies')" placeholder="Antecedentes de patologias respiratorias"></vue-input>
        </b-form-row>
    </b-card>

    <b-card bg-variant="transparent" border-variant="primary" title="" class="mb-3 box-shadow-none" style="padding-top: 15px;">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.type_of_exam" label="Tipo de examen" type="text" name="type_of_exam" :error="form.errorsFor('type_of_exam')" placeholder="Tipo de examen"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.date_of_realization" label="Fecha realización" type="text" name="date_of_realization" :error="form.errorsFor('date_of_realization')" placeholder="Fecha realización"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.cvf_average_percentage" label="CVF % promedio" type="text" name="cvf_average_percentage" :error="form.errorsFor('cvf_average_percentage')" placeholder="CVF % promedio"></vue-input>
        </b-form-row>

        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.vef1_average_percentage" label="VEF 1% promedio" type="text" name="vef1_average_percentage" :error="form.errorsFor('vef1_average_percentage')" placeholder="VEF 1% promedio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.vef1_cvf_average" label="VEF1 / CVF % promedio" type="text" name="vef1_cvf_average" :error="form.errorsFor('vef1_cvf_average')" placeholder="VEF1 / CVF % promedio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.interpretation" label="Interpretación" type="text" name="interpretation" :error="form.errorsFor('interpretation')" placeholder="Interpretación"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.classification_according_to_ats" label="Clasificación según ATS" type="text" name="classification_according_to_ats" :error="form.errorsFor('classification_according_to_ats')" placeholder="Clasificación según ATS"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.ats_obstruction_classification" label="Clasificación obstrucción ATS" type="text" name="ats_obstruction_classification" :error="form.errorsFor('ats_obstruction_classification')" placeholder="Clasificación obstrucción ATS"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.ats_restrictive_classification" label="Clasificación restrictivo ATS" type="text" name="ats_restrictive_classification" :error="form.errorsFor('ats_restrictive_classification')" placeholder="Clasificación restrictivo ATS"></vue-input>
        </b-form-row>
        
    </b-card>

    <div class="row float-right pt-10 pr-10" v-if="inForm">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <!--<b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>-->
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    inForm: { type: Boolean, default: true },
    analisy: {
      default() {
        return {
            name: ''
        };
      }
    }
  },
  watch: {
    analisy() {
      this.loading = false;
      this.form = Form.makeFrom(this.analisy, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.analisy, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
            this.loading = false;
            this.$router.push({ name: "biologicalmonitoring-respiratoryanalysis" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
