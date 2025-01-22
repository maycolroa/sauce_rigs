<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
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
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    cancelUrl: { type: [String, Object], required: true },
    custom: { type: Boolean, default: false },
    type: {
      default() {
        return {
            name: '',
            custom: this.custom
        };
      }
    }
  },
  watch: {
    type() {
      this.loading = false;
      this.form = Form.makeFrom(this.type, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.type, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;

          if (this.custom)
            this.$router.push({ name: "legalaspects-lm-type-company" });
          else
            this.$router.push({ name: "legalaspects-lm-type" });

        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
