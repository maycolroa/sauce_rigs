<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>

       <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_headquarter" name="employee_headquarter_id" label="Regional / Sede" placeholder="Seleccione la sede" :url="headquartersDataUrl">
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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    headquartersDataUrl: { type: String, default: "" },
    area: {
      default() {
        return {
            name: '',
            employee_headquarter_id: ''
        };
      }
    }
  },
  watch: {
    area() {
      this.loading = false;
      this.form = Form.makeFrom(this.area, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.area, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "administrative-areas" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
