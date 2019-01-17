<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.module_id" :multiple="false" :options="modules" :hide-selected="false" name="module_id" :error="form.errorsFor('module_id')" label="Aplicación / Módulo" placeholder="Seleccione el módulo">
        </vue-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-radio :disabled="viewOnly" :checked="form.location_level_form" class="col-md-12" v-model="form.location_level_form" :options="locationLevels" name="location_level_form" :error="form.errorsFor('location_level_form')" label="Nivel localización en formulario">
        </vue-radio>
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
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    modules: {
      type: Array,
      default: function() {
        return [];
      }
    },
    locationLevels: {
      type: Array,
      default: function() {
        return [];
      }
    },
    locationLevelForm: {
      default() {
        return {
          module_id: '',
          location_level_form: ''
        };
      }
    }
  },
  watch: {
    locationLevelForm() {
      this.loading = false;
      this.form = Form.makeFrom(this.locationLevelForm, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.locationLevelForm, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "configurations-locationlevelform" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
