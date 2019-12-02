<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card bg-variant="transparent" border-variant="dark" title="Datos Personales" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.patient_identification" label="Identificación" type="text" name="patient_identification" :error="form.errorsFor('patient_identification')" placeholder="Identificación"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.evaluation_type" label="Tipo de evaluación" type="text" name="evaluation_type" :error="form.errorsFor('evaluation_type')" placeholder="Tipo de evaluación"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.eps" :label="keywordCheck('eps')" type="text" name="eps" :error="form.errorsFor('eps')" placeholder="EPS"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.name" label="Nombre y Apellido" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre y Apellido"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.evaluation_format" label="Formato evaluación" type="text" name="evaluation_format" :error="form.errorsFor('evaluation_format')" placeholder="Formato evaluación"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.afp" :label="keywordCheck('afp')" type="text" name="afp" :error="form.errorsFor('afp')" placeholder="AFP"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.deal" label="Negocio" type="text" name="deal" :error="form.errorsFor('deal')" placeholder="Negocio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.age" label="Edad" type="text" name="age" :error="form.errorsFor('age')" placeholder="Edad"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.position" :label="keywordCheck('position')" type="text" name="position" :error="form.errorsFor('position')" placeholder="Seleccione una opción"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.branch_office" label="Localidad" type="text" name="branch_office" :error="form.errorsFor('branch_office')" placeholder="Localidad"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.phone" label="Telefóno" type="text" name="phone" :error="form.errorsFor('phone')" placeholder="Telefóno"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.antiquity" label="Antiguedad" type="text" name="antiquity" :error="form.errorsFor('antiquity')" placeholder="Antiguedad"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.sex" label="Sexo" type="text" name="sex" :error="form.errorsFor('sex')" placeholder="Sexo"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.phone_alternative" label="Telefóno Alterno" type="text" name="phone_alternative" :error="form.errorsFor('phone_alternative')" placeholder="Telefóno Alterno"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.state" label="Estado" type="text" name="state" :error="form.errorsFor('state')" placeholder="Estado"></vue-input>
        </b-form-row>
    </b-card>

    <b-card bg-variant="transparent" border-variant="primary" title="Habitos" class="mb-3 box-shadow-none" style="padding-top: 15px;">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.ant_atep_ep" label="Ant. ATEP-EP" type="text" name="ant_atep_ep" :error="form.errorsFor('ant_atep_ep')" placeholder="Ant. ATEP-EP"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.which_ant_atep_ep" label="Cuales Ant. ATEP-EP" type="text" name="which_ant_atep_ep" :error="form.errorsFor('which_ant_atep_ep')" placeholder="Cuales Ant. ATEP-EP"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.cigarette_habit" label="Habito Cigarrillo" type="text" name="cigarette_habit" :error="form.errorsFor('cigarette_habit')" placeholder="Habito Cigarrillo"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.exercise_habit" label="Habito Ejercicio" type="text" name="exercise_habit" :error="form.errorsFor('exercise_habit')" placeholder="Habito Ejercicio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.exercise_frequency" label="Frecuencia Ejercicio" type="text" name="exercise_frequency" :error="form.errorsFor('exercise_frequency')" placeholder="Frecuencia Ejercicio"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.cigarette_frequency" label="Frecuencia Cigarrillo" type="text" name="cigarette_frequency" :error="form.errorsFor('cigarette_frequency')" placeholder="Frecuencia Cigarrillo"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.liquor_habit" label="Habito Licor" type="text" name="liquor_habit" :error="form.errorsFor('liquor_habit')" placeholder="Habito Licor"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.liquor_frequency" label="Frecuencia Licor" type="text" name="liquor_frequency" :error="form.errorsFor('liquor_frequency')" placeholder="Frecuencia Licor"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.habit_extra_smoker" label="Habito Exfumador" type="text" name="habit_extra_smoker" :error="form.errorsFor('habit_extra_smoker')" placeholder="Habito Exfumador"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.exercise_habit" label="Habito Exbebedor" type="text" name="exercise_habit" :error="form.errorsFor('exercise_habit')" placeholder="Habito Exbebedor"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.liquor_suspension_time" label="Tiempo Suspensión Licor" type="text" name="liquor_suspension_time" :error="form.errorsFor('liquor_suspension_time')" placeholder="Tiempo Suspensión Licor"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.cigarrillo_suspension_time" label="Tiempo Suspensión Cigarrillo" type="text" name="cigarrillo_suspension_time" :error="form.errorsFor('cigarrillo_suspension_time')" placeholder="Tiempo Suspensión Cigarrillo"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.activity_extra_labor" label="Actividad Extralaboral" type="text" name="activity_extra_labor" :error="form.errorsFor('activity_extra_labor')" placeholder="Actividad Extralaboral"></vue-input>
        </b-form-row>
    </b-card>

    <b-card bg-variant="transparent" border-variant="dark" title="Examen Fisico" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.weight" label="Peso" type="text" name="weight" :error="form.errorsFor('weight')" placeholder="Peso"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.size" label="Estatura" type="text" name="size" :error="form.errorsFor('size')" placeholder="Estatura"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.imc" label="IMC" type="text" name="imc" :error="form.errorsFor('imc')" placeholder="IMC"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.imc_lassification" label="Clasificación IMC" type="text" name="imc_lassification" :error="form.errorsFor('imc_lassification')" placeholder="Clasificación IMC"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.abdominal_perimeter" label="Perímetro Abdominal" type="text" name="abdominal_perimeter" :error="form.errorsFor('abdominal_perimeter')" placeholder="Perímetro Abdominal"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.abdominal_perimeter_classification" label="Clas. Peri. Adominal" type="text" name="abdominal_perimeter_classification" :error="form.errorsFor('abdominal_perimeter_classification')" placeholder="Clas. Peri. Adominal"></vue-input>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_1 && form.diagnostic_1)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_1" label="Cód Diagnóstico 1" type="text" name="diagnostic_code_1" :error="form.errorsFor('diagnostic_code_1')" placeholder="Cód Diagnóstico 1"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_1" label="Diagnóstico 1" :error="form.errorsFor('diagnostic_1')" name="diagnostic_1" placeholder="Diagnóstico 1"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_2 && form.diagnostic_2)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_2" label="Cód Diagnóstico 2" type="text" name="diagnostic_code_2" :error="form.errorsFor('diagnostic_code_2')" placeholder="Cód Diagnóstico 2"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_2" label="Diagnóstico 2" :error="form.errorsFor('diagnostic_2')" name="diagnostic_2" placeholder="Diagnóstico 2"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_3 && form.diagnostic_3)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_3" label="Cód Diagnóstico 3" type="text" name="diagnostic_code_3" :error="form.errorsFor('diagnostic_code_3')" placeholder="Cód Diagnóstico 3"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_3" label="Diagnóstico 3" :error="form.errorsFor('diagnostic_3')" name="diagnostic_3" placeholder="Diagnóstico 3"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_4 && form.diagnostic_4)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_3" label="Cód Diagnóstico 4" type="text" name="diagnostic_code_4" :error="form.errorsFor('diagnostic_code_4')" placeholder="Cód Diagnóstico 4"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_4" label="Diagnóstico 4" :error="form.errorsFor('diagnostic_4')" name="diagnostic_4" placeholder="Diagnóstico 4"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_5 && form.diagnostic_5)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_5" label="Cód Diagnóstico 5" type="text" name="diagnostic_code_5" :error="form.errorsFor('diagnostic_code_5')" placeholder="Cód Diagnóstico 5"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_5" label="Diagnóstico 5" :error="form.errorsFor('diagnostic_5')" name="diagnostic_5" placeholder="Diagnóstico 5"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_6 && form.diagnostic_6)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_6" label="Cód Diagnóstico 6" type="text" name="diagnostic_code_6" :error="form.errorsFor('diagnostic_code_6')" placeholder="Cód Diagnóstico 6"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_6" label="Diagnóstico 6" :error="form.errorsFor('diagnostic_6')" name="diagnostic_6" placeholder="Diagnóstico 6"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_7 && form.diagnostic_7)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_7" label="Cód Diagnóstico 7" type="text" name="diagnostic_code_7" :error="form.errorsFor('diagnostic_code_7')" placeholder="Cód Diagnóstico 7"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_7" label="Diagnóstico 7" :error="form.errorsFor('diagnostic_7')" name="diagnostic_7" placeholder="Diagnóstico 7"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_8 && form.diagnostic_8)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_8" label="Cód Diagnóstico 8" type="text" name="diagnostic_code_8" :error="form.errorsFor('diagnostic_code_8')" placeholder="Cód Diagnóstico 8"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_8" label="Diagnóstico 8" :error="form.errorsFor('diagnostic_8')" name="diagnostic_8" placeholder="Diagnóstico 8"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_9 && form.diagnostic_9)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_9" label="Cód Diagnóstico 9" type="text" name="diagnostic_code_9" :error="form.errorsFor('diagnostic_code_9')" placeholder="Cód Diagnóstico 9"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_9" label="Diagnóstico 9" :error="form.errorsFor('diagnostic_9')" name="diagnostic_9" placeholder="Diagnóstico 9"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_10 && form.diagnostic_10)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_10" label="Cód Diagnóstico 10" type="text" name="diagnostic_code_10" :error="form.errorsFor('diagnostic_code_10')" placeholder="Cód Diagnóstico 10"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_10" label="Diagnóstico 10" :error="form.errorsFor('diagnostic_10')" name="diagnostic_10" placeholder="Diagnóstico 10"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_11 && form.diagnostic_11)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_11" label="Cód Diagnóstico 11" type="text" name="diagnostic_code_11" :error="form.errorsFor('diagnostic_code_11')" placeholder="Cód Diagnóstico 11"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_11" label="Diagnóstico 11" :error="form.errorsFor('diagnostic_11')" name="diagnostic_11" placeholder="Diagnóstico 11"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_12 && form.diagnostic_12)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_12" label="Cód Diagnóstico 12" type="text" name="diagnostic_code_12" :error="form.errorsFor('diagnostic_code_12')" placeholder="Cód Diagnóstico 12"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_12" label="Diagnóstico 12" :error="form.errorsFor('diagnostic_12')" name="diagnostic_12" placeholder="Diagnóstico 12"></vue-textarea>
        </b-form-row>
        <b-form-row v-if="!viewOnly || (viewOnly && form.diagnostic_code_13 && form.diagnostic_13)">
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.diagnostic_code_13" label="Cód Diagnóstico 13" type="text" name="diagnostic_code_13" :error="form.errorsFor('diagnostic_code_13')" placeholder="Cód Diagnóstico 13"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.diagnostic_13" label="Diagnóstico 13" :error="form.errorsFor('diagnostic_13')" name="diagnostic_13" placeholder="Diagnóstico 13"></vue-textarea>
        </b-form-row>
    </b-card>

    <b-card bg-variant="transparent" border-variant="primary" title="Semaforización" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.age_risk" label="Riesgo Edad (A)" type="text" name="age_risk" :error="form.errorsFor('age_risk')" placeholder="Riesgo Edad (A)"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.cardiovascular_risk" label="Riesgo Cardiovascular" type="text" name="cardiovascular_risk" :error="form.errorsFor('cardiovascular_risk')" placeholder="Riesgo Cardiovascular"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.pathological_background_risks" label="Riesgos Ant Patológicos (B)" type="text" name="pathological_background_risks" :error="form.errorsFor('pathological_background_risks')" placeholder="Riesgos Ant Patológicos (B)"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.osteomuscular_classification" label="Clasificación OM" type="text" name="osteomuscular_classification" :error="form.errorsFor('osteomuscular_classification')" placeholder="Clasificación OM"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.consolidated_personal_risk_criterion" label="Consolidado Riesgo Per (Criterio)" type="text" name="consolidated_personal_risk_criterion" :error="form.errorsFor('consolidated_personal_risk_criterion')" placeholder="Consolidado Riesgo Per (Criterio)"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.extra_labor_activities_risk" label="Riesgo Actividades Ext (C )" type="text" name="extra_labor_activities_risk" :error="form.errorsFor('extra_labor_activities_risk')" placeholder="Riesgo Actividades Ext (C )"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.osteomuscular_group" label="Grupo OM" type="text" name="osteomuscular_group" :error="form.errorsFor('osteomuscular_group')" placeholder="Grupo OM"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.prioritization_medical_criteria" label="Criterio Médico de Priorización" type="text" name="prioritization_medical_criteria" :error="form.errorsFor('prioritization_medical_criteria')" placeholder="Criterio Médico de Priorización"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.sedentary_risk" label="Riesgo Sedentarismo (D)" type="text" name="sedentary_risk" :error="form.errorsFor('sedentary_risk')" placeholder="Riesgo Sedentarismo (D)"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.consolidated_personal_risk_punctuation" label="Riesgo Per.(Puntuación)" type="text" name="consolidated_personal_risk_punctuation" :error="form.errorsFor('consolidated_personal_risk_punctuation')" placeholder="Riesgo Per.(Puntuación)"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.imc_risk" label="Riesgo IMC (E)" type="text" name="imc_risk" :error="form.errorsFor('imc_risk')" placeholder="Riesgo IMC (E)"></vue-input>
        </b-form-row>
    </b-card>

    <b-card bg-variant="transparent" border-variant="dark" title="Recomendaciones" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.observations" label="Observaciones" :error="form.errorsFor('observations')" name="observations" placeholder="Observaciones"></vue-textarea>
            <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.recommendations" label="Recomendaciones " :error="form.errorsFor('recommendations')" name="recommendations" placeholder="Recomendaciones "></vue-textarea>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.concept" label="Concepto" type="text" name="concept" :error="form.errorsFor('concept')" placeholder="Concepto"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-4" v-model="form.remission" label="Remisión" type="text" name="remission" :error="form.errorsFor('remission')" placeholder="Remisión"></vue-input>
            <vue-textarea :disabled="viewOnly" class="col-md-4" v-model="form.restrictions" label="Restricciones " :error="form.errorsFor('restrictions')" name="restrictions" placeholder="Restricciones "></vue-textarea>
        </b-form-row>
    </b-card>

    <b-card bg-variant="transparent" border-variant="dark" title="Sintomatologia" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.description_medical_exam" label="Descripcion Examen medico" :error="form.errorsFor('description_medical_exam')" name="description_medical_exam" placeholder="Descripcion Examen medico"></vue-textarea>
            <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.symptom" label="Sintomas " :error="form.errorsFor('symptom')" name="symptom" placeholder="Sintomas "></vue-textarea>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.symptom_type" label="Tipo de sintomas" type="text" name="symptom_type" :error="form.errorsFor('symptom_type')" placeholder="Tipo de sintomas"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.body_part" label="Parte del cuerpo" type="text" name="body_part" :error="form.errorsFor('body_part')" placeholder="Parte del cuerpo"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.periodicity" label="Periodicidad" type="text" name="periodicity" :error="form.errorsFor('periodicity')" placeholder="Periodicidad"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.workday" label="Jornada Laboral" type="text" name="workday" :error="form.errorsFor('workday')" placeholder="Jornada Laboral"></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.symptomatology_observations" label="Observaciones " :error="form.errorsFor('symptomatology_observations')" name="symptomatology_observations" placeholder="Observaciones "></vue-textarea>
        </b-form-row>
    </b-card>

    <div class="row float-right pt-10 pr-10" v-if="inForm">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
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
            this.$router.push({ name: "biologicalmonitoring-musculoskeletalanalysis" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
