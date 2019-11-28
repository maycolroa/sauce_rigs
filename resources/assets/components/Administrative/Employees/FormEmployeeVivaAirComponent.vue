<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.identification" label="Identificación" type="text" name="identification" :error="form.errorsFor('identification')" placeholder="Identificación"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date_of_birth" label="Fecha de nacimiento" :full-month-name="true" placeholder="Seleccione la fecha de nacimiento" :error="form.errorsFor('date_of_birth')" name="date_of_birth" :disabled-dates="disabledDates">
          </vue-datepicker>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.sex" :error="form.errorsFor('sex')" :multiple="false" :options="sexs" :hide-selected="false" name="sex" label="Sexo" placeholder="Seleccione el sexo">
          </vue-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.mobile" label="Celular" type="number" name="mobile" :error="form.errorsFor('mobile')" placeholder="Celular"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.extension" label="Teléfono" type="text" name="extension" :error="form.errorsFor('extension')" placeholder="Teléfono"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.income_date" label="Fecha de Ingreso" :full-month-name="true" placeholder="Seleccione la fecha de ingreso" :error="form.errorsFor('income_date')" name="income_date" :disabled-dates="disabledDates">
          </vue-datepicker>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regional')" placeholder="Seleccione una opción" :url="regionalsDataUrl">
          </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_regional_id" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_sede" name="employee_headquarter_id" :label="keywordCheck('headquarter')" placeholder="Seleccione una opción" :url="headquartersDataUrl" :parameters="{regional: form.employee_regional_id }" :emptyAll="empty.headquarter" @updateEmpty="updateEmptyKey('headquarter')">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_headquarter_id" class="col-md-6" v-model="form.employee_process_id" :error="form.errorsFor('employee_process_id')" :selected-object="form.multiselect_proceso" name="employee_process_id" :label="keywordCheck('process')" placeholder="Seleccione una opción" :url="processesDataUrl" :parameters="{headquarter: form.employee_headquarter_id }" :emptyAll="empty.process" @updateEmpty="updateEmptyKey('process')">
      </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_process_id" class="col-md-6" v-model="form.employee_area_id" :error="form.errorsFor('employee_area_id')" :selected-object="form.multiselect_area" name="employee_area_id" :label="keywordCheck('area')" placeholder="Seleccione una opción" :url="areasDataUrl" :parameters="{process: form.employee_process_id, headquarter: form.employee_headquarter_id }" :emptyAll="empty.area" @updateEmpty="updateEmptyKey('area')">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_position_id" :error="form.errorsFor('employee_position_id')" :selected-object="form.multiselect_cargo" name="employee_position_id" :label="keywordCheck('position')" placeholder="Seleccione una opción" :url="positionsDataUrl">
          </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_business_id" :error="form.errorsFor('employee_business_id')" :selected-object="form.multiselect_centro_costo" name="employee_business_id" :label="keywordCheck('businesses')" placeholder="Seleccione una opción" :url="businessesDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>
      
    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_eps_id" :error="form.errorsFor('employee_eps_id')" :selected-object="form.multiselect_eps" name="employee_eps_id" :label="keywordCheck('eps')" placeholder="Seleccione una opción" :url="epsDataUrl">
      </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_afp_id" :error="form.errorsFor('employee_afp_id')" :selected-object="form.multiselect_afp" name="employee_afp_id" :label="keywordCheck('afp')" placeholder="Seleccione una opción" :url="afpDataUrl">
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
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput
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
    positionsDataUrl: { type: String, default: "" },
    businessesDataUrl: { type: String, default: "" },
    epsDataUrl: { type: String, default: "" },
    afpDataUrl: { type: String, default: "" },
    disableWacthSelectInCreated: { type: Boolean, default: false},
    sexs: {
      type: Array,
      default: function() {
        return [];
      }
    },
    employee: {
      default() {
        return {
            identification: '',
            name: '',
            date_of_birth: '',
            sex: '',
            email: '',
            income_date: '',
            employee_regional_id: '',
            employee_headquarter_id: '',
            employee_area_id: '',
            employee_process_id: '',
            employee_position_id: '',
            employee_business_id: '',
            employee_eps_id: '',
            employee_afp_id: '',
            deal:'',
            extension: '',
            mobile: ''
        };
      }
    }
  },
  watch: {
    employee() {
      this.form = Form.makeFrom(this.employee, this.method);
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
      form: Form.makeFrom(this.employee, this.method),
      empty: {
        headquarter: false,
        area: false,
        process: false
      },
      disableWacth: this.disableWacthSelectInCreated,

      disabledDates: {
        from: new Date()
      }
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
          this.$router.push({ name: "administrative-employees" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
