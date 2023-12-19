<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
     <b-form-row>
      <vue-input :disabled="true" class="col-md-6" v-model="form.title" label="Título" type="text" name="title" :error="form.errorsFor('title')" placeholder="Título"></vue-input>
      <vue-textarea :disabled="true" class="col-md-6" v-model="form.description" label="Descripción" type="text" name="description" :error="form.errorsFor('description')" placeholder="Descripción"></vue-textarea>
    </b-form-row>
    <br><br><br>
    <b-form-row v-if="form.file && form.file.type == 'pdf'">
      <iframe style="width: 100%; height: 700px;" frameborder="0" id="frame_imprimir_rendicion" title="IMPRIMIR" :src="form.file.path"></iframe>
    </b-form-row>
    <b-form-row v-if="form.file && form.file.type == 'mp4'">
      <video style="width: 100%; height: 600px;" controls>
        <source :src="form.file.path" type="video/mp4">
      </video>
    </b-form-row>
    <br><br><br>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueTextarea,
    VueFileSimple,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    help: {
      default() {
        return {
            title: '',
            description: '',
            module_id: '',
            file: {
                key: new Date().getTime(),
                name: '',
                file: '',
                path: '',
                type: ''
            },
        };
      }
    }
  },
  watch: {
    help() {
      this.loading = false;
      this.form = Form.makeFrom(this.help, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.help, this.method)
    };
  },
  mounted() {},
  methods: {}
};
</script>
