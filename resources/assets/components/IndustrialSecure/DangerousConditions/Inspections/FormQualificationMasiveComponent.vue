<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-advanced-select 
        v-model="form.options"
        class="col-md-12" :multiple="true" :selectedObject="form.options" :options="optionQualification" :hide-selected="false" name="options" label="Seleccione las calificaciones" placeholder="Seleccione las calificaciones"
        :error="form.errorsFor('options')" >
        </vue-advanced-select>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    optionQualification: {
      type: Array,
      default: function() {
        return [];
      }
    },
    configuration: {
      default() {
        return {
          options: [],
        };
      }
    }
  },
  watch: {
    configuration() {
      this.loading = false;
      this.form = Form.makeFrom(this.configuration, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.configuration, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "dangerousconditions-inspections" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
