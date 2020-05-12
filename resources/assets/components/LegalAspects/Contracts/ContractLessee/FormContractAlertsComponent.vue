<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-input class="col-md-12" v-model="form.days_alert_without_activity" label="Días de alerta para contratistas sin actividad" type="number" name="days_alert_without_activity" :error="form.errorsFor('days_alert_without_activity')" placeholder="1" min="1"></vue-input>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
      </template>
    </div>

  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    configuration: {
      default() {
        return {
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
