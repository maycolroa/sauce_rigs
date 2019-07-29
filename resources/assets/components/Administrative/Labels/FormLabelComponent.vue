<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.keyword_id" :error="form.errorsFor('keyword_id')" :selected-object="form.multiselect_keyword" name="keyword_id" label="Etiqueta" placeholder="Seleccione la Etiqueta" :url="labelsUrlData">
          </vue-ajax-advanced-select>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.display_name" label="Descripción" type="text" name="display_name" :error="form.errorsFor('display_name')" placeholder="Descripción" help-text="Nombre en el sistema que será mostrado en reemplazo de la etiqueta seleccionada"></vue-input>
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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    labelsUrlData: { type: String, default: '' },
    label: {
      default() {
        return {
          keyword_id: '',
          display_name: ''
        };
      }
    }
  },
  watch: {
    label() {
      this.loading = false;
      this.form = Form.makeFrom(this.label, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.label, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "administrative-customlabels" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
