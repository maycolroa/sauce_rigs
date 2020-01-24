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
      <location-level-component
        :is-edit="isEdit"
        :view-only="viewOnly"
        v-model="form.locations"
        :location-level="employee.locations"
        :form="form"/>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_position_id" :error="form.errorsFor('employee_position_id')" :selected-object="form.multiselect_cargo" name="employee_position_id" :label="keywordCheck('position')" placeholder="Seleccione una opción" :url="positionsDataUrl">
          </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_business_id" :error="form.errorsFor('employee_business_id')" :selected-object="form.multiselect_centro_costo" name="employee_business_id" :label="keywordCheck('businesses')" placeholder="Seleccione una opción" :url="businessesDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.deal" label="Negocio" type="text" name="deal" :error="form.errorsFor('deal')" placeholder="Negocio"></vue-input>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_eps_id" :error="form.errorsFor('employee_eps_id')" :selected-object="form.multiselect_eps" name="employee_eps_id" :label="keywordCheck('eps')" placeholder="Seleccione una opción" :url="epsDataUrl">
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
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    LocationLevelComponent
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    positionsDataUrl: { type: String, default: "" },
    businessesDataUrl: { type: String, default: "" },
    epsDataUrl: { type: String, default: "" },
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
            employee_position_id: '',
            employee_business_id: '',
            employee_eps_id: '',
            deal:'',
            extension: '',
            mobile: '',
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
    employee() {
      this.form = Form.makeFrom(this.employee, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.employee, this.method),

      disabledDates: {
        from: new Date()
      }
    };
  },
  methods: {
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
