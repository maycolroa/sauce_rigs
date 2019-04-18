<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c'] && !auth.can['configurations_u']" :checked="form.location_level_form" class="col-md-12" v-model="form.location_level_form" :options="locationLevels" name="location_level_form" :error="form.errorsFor('location_level_form')" label="Nivel localización en formulario">
        </vue-radio>
    </b-form-row>

    <hr class="border-light container-m--x mt-0 mb-4">
    
    <b-form-row>
        <vue-input :disabled="!auth.can['configurations_c'] && !auth.can['configurations_u']" class="col-md-12" v-model="form.days_alert_expiration_date_action_plan" label="Días de alerta por fecha de vencimiento cercana para los plane de acción" type="number" name="days_alert_expiration_date_action_plan" :error="form.errorsFor('days_alert_expiration_date_action_plan')" placeholder="1"></vue-input>
    </b-form-row>

    <hr class="border-light container-m--x mt-0 mb-4">
    
    <b-form-row>
        <vue-input :disabled="!auth.can['configurations_c'] && !auth.can['configurations_u']" class="col-md-12" v-model="form.days_alert_expiration_date_contract_file_upload" label="Días de alerta por fecha de vencimiento cercana para los archivos cargados en el modulo de contratistas" type="number" name="days_alert_expiration_date_contract_file_upload" :error="form.errorsFor('days_alert_expiration_date_contract_file_upload')" placeholder="1"></vue-input>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading || (!auth.can['configurations_c'] && !auth.can['configurations_u'])" variant="primary">Guardar</b-btn>
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
    configuration: {
      default() {
        return {
          location_level_form: '',
          days_alert_expiration_date_action_plan: '',
          days_alert_expiration_date_contract_file_upload: ''
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
