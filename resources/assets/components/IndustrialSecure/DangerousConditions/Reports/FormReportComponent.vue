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
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regional')" :placeholder="`Seleccione la ${this.keywordCheck('regional')}`" :url="regionalsDataUrl">
          </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_regional_id" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_sede" name="employee_headquarter_id" label="Sede" placeholder="Seleccione la sede" :url="headquartersDataUrl" :parameters="{regional: form.employee_regional_id }" :emptyAll="empty.headquarter" @updateEmpty="updateEmptyKey('headquarter')">
          </vue-ajax-advanced-select>
   </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_headquarter_id" class="col-md-6" v-model="form.employee_process_id" :error="form.errorsFor('employee_process_id')" :selected-object="form.multiselect_proceso" name="employee_process_id" label="Proceso" placeholder="Seleccione el proceso" :url="processesDataUrl" :parameters="{headquarter: form.employee_headquarter_id }" :emptyAll="empty.process" @updateEmpty="updateEmptyKey('process')">
          </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_process_id" class="col-md-6" v-model="form.employee_area_id" :error="form.errorsFor('employee_area_id')" :selected-object="form.multiselect_area" name="employee_area_id" label="Área" placeholder="Seleccione el área" :url="areasDataUrl" :parameters="{process: form.employee_process_id, headquarter: form.employee_headquarter_id }" :emptyAll="empty.area" @updateEmpty="updateEmptyKey('area')">
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueTextarea
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
    disableWacthSelectInCreated: { type: Boolean, default: false},
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
            employee_regional_id: '',
            employee_headquarter_id: '',
            employee_area_id: '',
            employee_process_id: '',
            other_condition: ''
        };
      }
    }
  },
  watch: {
    report() {
      this.form = Form.makeFrom(this.report, this.method);
    },
    'form.employee_regional_id'() {
      this.emptySelect('employee_process_id', 'process')
      this.emptySelect('employee_area_id', 'area')
      this.emptySelect('employee_headquarter_id', 'headquarter')
    },
    'form.employee_headquarter_id'() {
      this.emptySelect('employee_process_id', 'process')
      this.emptySelect('employee_area_id', 'area')
    },
    'form.employee_process_id'() {
      this.emptySelect('employee_area_id', 'area')
    },
    'form.employee_area_id'() {
      if (this.disableWacth)
        this.disableWacth = false
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.report, this.method),
      empty: {
        headquarter: false,
        area: false,
        process: false
      },
      disableWacth: this.disableWacthSelectInCreated
    };
  },
  methods: {
    updateEmptyKey(keyEmpty)
    {
      this.empty[keyEmpty]  = false
    },
    emptySelect(keySelect, keyEmpty)
    {
      if (this.form[keySelect] !== '' && !this.disableWacth)
      {
        this.empty[keyEmpty] = true
        this.form[keySelect] = ''
      }
    },
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
