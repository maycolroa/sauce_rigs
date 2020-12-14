<template>
  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
      
      <div v-html="termsConditions"></div>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading" variant="primary">Aceptar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import Form from "@/utils/Form.js";

export default {
  components: {
  },
  props: {
    url: { type: String },
    method: { type: String },
    termsConditions: { type: String, required: true }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom({}, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          window.location =  "/";
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
