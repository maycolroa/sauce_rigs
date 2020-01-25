<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.rate" :multiple="false" :options="rates" :hide-selected="false" name="rate" :error="form.errorsFor('rate')" label="Severidad" placeholder="Seleccione el grado de severidad">
        </vue-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.condition_id" :error="form.errorsFor('condition_id')" :selected-object="form.multiselect_condition" name="condition_id" label="Condición" placeholder="Seleccione la condición" :url="conditionsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.observation" label="Observación" name="observation" :error="form.errorsFor('observation')"  placeholder="Observación"></vue-textarea>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.other_condition" label="Otra Condición" name="other_condition" :error="form.errorsFor(`other_condition`)"  placeholder="Otra Condición"></vue-textarea>
    </b-form-row>
  
    <b-form-row>
      <location-level-component
        :is-edit="isEdit"
        :view-only="viewOnly"
        v-model="form.locations"
        :location-level="report.locations"
        :form="form"/>
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueTextarea,
    LocationLevelComponent
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    regionalsDataUrl: { type: String, default: "" },
    headquartersDataUrl: { type: String, default: "" },
    areasDataUrl: { type: String, default: "" },
    processesDataUrl: { type: String, default: "" },
    conditionsDataUrl: { type: String, default: "" },
    rates: { 
      type: Array,
      default: function() {
        return [];
      } 
    },
    report: {
      default() {
        return {
            observation: '',
            rate: '',
            condition_id: '',
            other_condition: '',
            locations: {
              employee_regional_id: '',
              employee_headquarter_id: '',
              employee_area_id: '',
              employee_process_id: ''
            }
        };
      }
    }
  },
  watch: {
    report() {
      this.form = Form.makeFrom(this.report, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.report, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "dangerousconditions-reports" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
