<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <b-form-row>
        <vue-file-simple :help-text="form.old_file ? `Para descargar el archivo actual, haga click <a href='/biologicalmonitoring/absenteeism/talendUpload/download/${form.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" accept=".zip" class="col-md-12" v-model="form.file" label="Archivo (*.zip)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>
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
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    talend: {
      default() {
        return {
            name: '',
            file: ''
        };
      }
    }
  },
  watch: {
    talend() {
      this.loading = false;
      this.form = Form.makeFrom(this.talend, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.talend, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "absenteeism-talends" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
