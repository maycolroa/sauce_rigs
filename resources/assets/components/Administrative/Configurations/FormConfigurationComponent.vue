<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row v-if="auth.can['biologicalMonitoring_audiometry_r'] || auth.can['dangerMatrix_r'] || auth.can['reinc_checks_r'] || auth.can['ph_inspections_r'] || auth.can['ph_reports_r'] || auth.can['biologicalMonitoring_musculoskeletalAnalysis_r'] || auth.can['biologicalMonitoring_respiratoryAnalysis_r']">
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.location_level_form" class="col-md-12" v-model="form.location_level_form" :options="locationLevels" name="location_level_form" :error="form.errorsFor('location_level_form')" label="Nivel localización en formulario">
        </vue-radio>
    </b-form-row>

    <hr class="border-light container-m--x mt-0 mb-4">
    
    <b-form-row v-if="auth.can['actionPlans_r']">
        <vue-input :disabled="!auth.can['configurations_c']" class="col-md-12" v-model="form.days_alert_expiration_date_action_plan" label="Días de alerta por fecha de vencimiento cercana para los plane de acción" type="number" name="days_alert_expiration_date_action_plan" :error="form.errorsFor('days_alert_expiration_date_action_plan')" placeholder="1"></vue-input>
    </b-form-row>

    <hr class="border-light container-m--x mt-0 mb-4">

    <b-form-row v-if="auth.can['contracts_r']">
      <vue-input :disabled="!auth.can['configurations_c']" class="col-md-12" v-model="form.days_alert_without_activity" label="Días de alerta para contratistas sin actividad" type="number" name="days_alert_without_activity" :error="form.errorsFor('days_alert_without_activity')" placeholder="1" min="1"></vue-input>
    </b-form-row>

    <hr class="border-light container-m--x mt-0 mb-4">
    
    <b-form-row v-if="auth.can['contracts_r']">
        <vue-input :disabled="!auth.can['configurations_c']" class="col-md-12" v-model="form.days_alert_expiration_date_contract_file_upload" label="Días de alerta por fecha de vencimiento cercana para los archivos cargados en el modulo de contratistas" type="number" name="days_alert_expiration_date_contract_file_upload" :error="form.errorsFor('days_alert_expiration_date_contract_file_upload')" placeholder="1"></vue-input>
    </b-form-row>

    <hr class="border-light container-m--x mt-0 mb-4">

    <b-form-row v-if="auth.can['dangerMatrix_r']">
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.show_action_plans" class="col-md-12" v-model="form.show_action_plans" :options="siNo" name="show_action_plans" :error="form.errorsFor('show_action_plans')" label="Mostrar planes de acción en el formulario de matriz de peligros">
        </vue-radio>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading || (!auth.can['configurations_c'])" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String },
    locationLevels: {
      type: Array,
      default: function() {
        return [];
      }
    },
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    configuration: {
      default() {
        return {
          location_level_form: '',
          days_alert_expiration_date_action_plan: '',
          days_alert_expiration_date_contract_file_upload: '',
          show_action_plans: '',
          days_alert_without_activity: ''
        };
      }
    }
  },
  watch: {
    configuration() {
      this.loading = false;
      this.form = Form.makeFrom(this.configuration, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.configuration, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
