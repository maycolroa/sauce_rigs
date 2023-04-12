<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.subject" label="Asunto" type="text" name="subject" :error="form.errorsFor('subject')" placeholder="Asunto"></vue-input>
    </b-form-row>
    <b-form-row> 
        <vue-file-simple :help-text="form.path ? `Para descargar el archivo actual, haga click <a href='/system/newsletterSend/downloadImage/${form.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-12" v-model="form.image" label="Imagen a enviar" name="image" accept=".png" :error="form.errorsFor('image')" placeholder="Seleccione una imagen" :maxFileSize="10"></vue-file-simple>
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueInput,
    VueRadio,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    newsletter: {
      default() {
        return {
          subject: '',
          image: ''
        };
      }
    }
  },
  watch: {
    newsletter() {
      this.loading = false;
      this.form = Form.makeFrom(this.newsletter, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.newsletter, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "system-newslettersend" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
