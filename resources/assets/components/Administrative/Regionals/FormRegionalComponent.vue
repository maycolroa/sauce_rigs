<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.abbreviation" label="Abreviatura (Opcional)" type="text" name="abbreviation" :error="form.errorsFor('abbreviation')" placeholder="Abreviatura"></vue-input>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" v-if="!modal" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
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
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    modal: { type: Boolean, default: false },
    regional: {
      default() {
        return {
            name: '',
            abbreviation: ''
        };
      }
    }
  },
  watch: {
    regional() {
      this.loading = false;
      this.form = Form.makeFrom(this.regional, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.regional, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;

          if (this.modal)
          {
            Object.assign(this.$data, this.$options.data.apply(this))
            this.$parent.$emit("closeModal")
          }
          else
            this.$router.push({ name: "administrative-regionals" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
