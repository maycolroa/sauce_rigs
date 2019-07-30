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
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.income_date" label="Fecha de Ingreso" :full-month-name="true" placeholder="Seleccione la fecha de ingreso" :error="form.errorsFor('income_date')" name="income_date" :disabled-dates="disabledDates">
          </vue-datepicker>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regional', 'Regional')" :placeholder="`Seleccione la ${this.keywordCheck('regional', 'Regional')}`" :url="regionalsDataUrl">
          </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_regional_id" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_sede" name="employee_headquarter_id" label="Sede" placeholder="Seleccione la sede" :url="headquartersDataUrl" :parameters="{regional: form.employee_regional_id }" :emptyAll="empty.headquarter" @updateEmpty="updateEmptyKey('headquarter')">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_headquarter_id" class="col-md-6" v-model="form.employee_process_id" :error="form.errorsFor('employee_process_id')" :selected-object="form.multiselect_proceso" name="employee_process_id" label="Proceso" placeholder="Seleccione el proceso" :url="processesDataUrl" :parameters="{headquarter: form.employee_headquarter_id }" :emptyAll="empty.process" @updateEmpty="updateEmptyKey('process')">
      </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_position_id" :error="form.errorsFor('employee_position_id')" :selected-object="form.multiselect_cargo" name="employee_position_id" label="Cargo" placeholder="Seleccione el cargo" :url="positionsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_business_id" :error="form.errorsFor('employee_business_id')" :selected-object="form.multiselect_centro_costo" name="employee_business_id" label="Centro de costo" placeholder="Seleccione el centro de costo" :url="businessesDataUrl">
          </vue-ajax-advanced-select>
          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_eps_id" :error="form.errorsFor('employee_eps_id')" :selected-object="form.multiselect_eps" name="employee_eps_id" label="EPS" placeholder="Seleccione el eps" :url="epsDataUrl">
      </vue-ajax-advanced-select>
    </b-form-row>
      
    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_afp_id" :error="form.errorsFor('employee_afp_id')" :selected-object="form.multiselect_afp" name="employee_afp_id" label="AFP" placeholder="Seleccione el afp" :url="afpDataUrl">
      </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_arl_id" :error="form.errorsFor('employee_arl_id')" :selected-object="form.multiselect_arl" name="employee_arl_id" label="ARL" placeholder="Seleccione el arl" :url="arlDataUrl">
      </vue-ajax-advanced-select>   
    </b-form-row>  

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.contract_numbers" label="Numero de contratos" type="number" name="contract_numbers" :error="form.errorsFor('contract_numbers')" placeholder="Numero de contratos"></vue-input>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.last_contract_date" label="Fecha de ultimo contrato" :full-month-name="true" placeholder="Seleccione la fecha de ultimo contrato" :error="form.errorsFor('last_contract_date')" name="last_contract_date" :disabled-dates="disabledDates">
          </vue-datepicker>      
    </b-form-row>

    <b-form-row>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.contract_type" :error="form.errorsFor('contract_type')" :multiple="false" :options="contracTypes" :hide-selected="false" name="contract_type" label="Tipo de contrato" placeholder="Seleccione el tipo de contrato">
          </vue-advanced-select>
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
    arlDataUrl: { type: String, default: "" },
    disableWacthSelectInCreated: { type: Boolean, default: false},
    sexs: {
      type: Array,
      default: function() {
        return [];
      }
    },
    contractTypes: {
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
            employee_process_id: '',
            employee_position_id: '',
            employee_business_id: '',
            employee_eps_id: '',
            employee_afp_id: '',
            employee_arl_id: '',
            contract_numbers: '',
            last_contract_date: '',
            contract_type: '',
            deal:''
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
      this.emptySelect('employee_areas_id', 'areas')
    },
    'form.employee_areas_id'() {
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
