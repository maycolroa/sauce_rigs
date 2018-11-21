<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
        <vue-advanced-select class="col-md-6" v-model="form.area" :error="form.errorsFor('area')" :multiple="true" :options="areas" :searchable="true" name="area" label="Área" placeholder="Seleccione las áreas">
        </vue-advanced-select>
        <vue-advanced-select class="col-md-6" v-model="form.regional" :error="form.errorsFor('regional')" :multiple="true" :options="regionales" :searchable="true" name="regional" label="Regional" placeholder="Seleccione las regionales">
        </vue-advanced-select>
    </b-form-row>

    <b-form-row>
        <vue-advanced-select class="col-md-6" v-model="form.year" :error="form.errorsFor('year')" :multiple="true" :options="years" :searchable="true" name="year" label="Año" placeholder="Seleccione los años">
        </vue-advanced-select>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading" variant="primary">Consultar</b-btn>
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
    areas: {
      type: Array,
      default: function() {
        return [];
      }
    },
    regionales: {
      type: Array,
      default: function() {
        return [];
      }
    },
    years: {
      type: Array,
      default: function() {
        return [];
      }
    },
    information: {
      default() {
        return {
            area: '',
            regional: '',
            year: ''
        };
      }
    }
  },
  watch: {
    information() {
      this.loading = false;
      this.form = Form.makeFrom(this.information, this.method, false, false);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.information, this.method, false, false),
    };
  },
  methods: {
    submit(e) {
    
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$parent.$emit('changeDataReportPta', response.data.data)
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
