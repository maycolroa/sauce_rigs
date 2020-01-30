<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card bg-variant="transparent" border-variant="dark" title="Datos Personales" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.patient_identification" label="Identificación" type="text" name="patient_identification" :error="form.errorsFor('patient_identification')" placeholder="Identificación"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.deal" label="Negocio" type="text" name="deal" :error="form.errorsFor('deal')" placeholder="Negocio"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.name" label="Nombre y Apellido" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre y Apellido"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.regional" label="Planta" type="text" name="regional" :error="form.errorsFor('regional')" placeholder="Planta"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.sex" label="Sexo" type="text" name="sex" :error="form.errorsFor('sex')" placeholder="Sexo"></vue-input>            
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.antiquity" label="Antiguedad" type="text" name="antiquity" :error="form.errorsFor('antiquity')" placeholder="Antiguedad"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.age" label="Edad" type="text" name="age" :error="form.errorsFor('age')" placeholder="Edad"></vue-input>            
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.state" label="Estado" type="text" name="state" :error="form.errorsFor('state')" placeholder="Estado"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.position" :label="keywordCheck('position')" type="text" name="position" :error="form.errorsFor('position')" placeholder="Seleccione una opción"></vue-input>            
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.symptomatology" label="Sintomatología" type="text" name="symptomatology" :error="form.errorsFor('symptomatology')" placeholder="Sintomatología"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.area" label="Área" type="text" name="area" :error="form.errorsFor('area')" placeholder="Área"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.habits" label="Hábitos" type="text" name="habits" :error="form.errorsFor('habits')" placeholder="Hábitos"></vue-input>            
        </b-form-row>
        <b-form-row>   
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.history_of_respiratory_pathologies" label="Antecedentes Respiratorios" type="text" name="history_of_respiratory_pathologies" :error="form.errorsFor('history_of_respiratory_pathologies')" placeholder="Antecedentes Respiratorios"></vue-input>        
        </b-form-row>
    </b-card>

    <b-card bg-variant="transparent" border-variant="primary" title="" class="mb-3 box-shadow-none" style="padding-top: 15px;">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.measurement_date" label="Fecha de realización de mediciones" type="text" name="measurement_date" :error="form.errorsFor('measurement_date')" placeholder="Fecha de realización de mediciones"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.mg_m3_concentration" label="Concentración mg m3" type="text" name="mg_m3_concentration" :error="form.errorsFor('mg_m3_concentration')" placeholder="Concentración mg m3"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.ir" label="IR" type="text" name="ir" :error="form.errorsFor('ir')" placeholder="IR"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.type_of_exam" label="Tipo de examen" type="text" name="type_of_exam" :error="form.errorsFor('type_of_exam')" placeholder="Tipo de examen"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.year_of_spirometry" label="Año de espirometria" type="text" name="year_of_spirometry" :error="form.errorsFor('year_of_spirometry')" placeholder="Año de espirometria"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.spirometry" label="Espirometria" type="text" name="spirometry" :error="form.errorsFor('spirometry')" placeholder="Espirometria"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.date_of_realization" label="Fecha realización" type="text" name="date_of_realization" :error="form.errorsFor('date_of_realization')" placeholder="Fecha realización"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.cvf_average_percentage" label="CVF % promedio" type="text" name="cvf_average_percentage" :error="form.errorsFor('cvf_average_percentage')" placeholder="CVF % promedio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.vef1_average_percentage" label="VEF 1% promedio" type="text" name="vef1_average_percentage" :error="form.errorsFor('vef1_average_percentage')" placeholder="VEF 1% promedio"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.vef1_cvf_average" label="VEF1 / CVF % promedio" type="text" name="vef1_cvf_average" :error="form.errorsFor('vef1_cvf_average')" placeholder="VEF1 / CVF % promedio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.fef_25_75_porcentage" label="FEF 25-75%" type="text" name="fef_25_75_porcentage" :error="form.errorsFor('fef_25_75_porcentage')" placeholder="FEF 25-75%"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.interpretation" label="Interpretación" type="text" name="interpretation" :error="form.errorsFor('interpretation')" placeholder="Interpretación"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.type_of_exam_2" label="Tipo de examen 2" type="text" name="type_of_exam_2" :error="form.errorsFor('type_of_exam_2')" placeholder="Tipo de examen 2"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.date_of_realization_2" label="Fecha de realización" type="text" name="date_of_realization_2" :error="form.errorsFor('date_of_realization_2')" placeholder="Fecha de realización"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.rx_oit" label="RX OIT" type="text" name="rx_oit" :error="form.errorsFor('rx_oit')" placeholder="RX OIT"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.quality" label="Calidad" type="text" name="quality" :error="form.errorsFor('quality')" placeholder="Calidad"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.yes_1" label="Sí 1" type="text" name="yes_1" :error="form.errorsFor('yes_1')" placeholder="Sí 1"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.not_1" label="No 1" type="text" name="not_1" :error="form.errorsFor('not_1')" placeholder="No 1"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.answer_yes_describe" label="Respuesta sí, describir" type="text" name="answer_yes_describe" :error="form.errorsFor('answer_yes_describe')" placeholder="Respuesta sí, describir"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.yes_2" label="Sí 2" type="text" name="yes_2" :error="form.errorsFor('yes_2')" placeholder="Sí 2"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.not_2" label="No 2" type="text" name="not_2" :error="form.errorsFor('not_2')" placeholder="No 2"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.answer_yes_describe_2" label="Respuesta sí, describir" type="text" name="answer_yes_describe_2" :error="form.errorsFor('answer_yes_describe_2')" placeholder="Respuesta sí, describir"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.other_abnormalities" label="Otras anormalidades" type="text" name="other_abnormalities" :error="form.errorsFor('other_abnormalities')" placeholder="Otras anormalidades"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.fully_negative" label="Totalmente negativa" type="text" name="fully_negative" :error="form.errorsFor('fully_negative')" placeholder="Totalmente negativa"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.observation" label="Observación" type="text" name="observation" :error="form.errorsFor('observation')" placeholder="Observación"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.breathing_problems" label="Problema Respiratorio" type="text" name="breathing_problems" :error="form.errorsFor('breathing_problems')" placeholder="Problema Respiratorio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.classification_according_to_ats" label="Clasificación según ATS" type="text" name="classification_according_to_ats" :error="form.errorsFor('classification_according_to_ats')" placeholder="Clasificación según ATS"></vue-input>
        </b-form-row>
        <b-form-row>
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
