<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-ajax-advanced-select v-if="auth.can['elements_r']" :disabled="viewOnly" class="col-md-12" v-model="form.elements_id" :error="form.errorsFor('elements_id')"  name="elements_id" label="Elementos de protección personal correspondientes al cargo" placeholder="Seleccione los elementos" :url="tagsElementsDataUrl" :multiple="true" :allowEmpty="true" :selected-object="form.multiselect_elements">
        </vue-ajax-advanced-select>
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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
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
    position: {
      default() {
        return {
            name: '',
            elements_id: []
        };
      }
    }
  },
  watch: {
    position() {
      this.loading = false;
      this.form = Form.makeFrom(this.position, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.position, this.method),
      tagsElementsDataUrl: '/selects/eppElements',
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "administrative-positions" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
