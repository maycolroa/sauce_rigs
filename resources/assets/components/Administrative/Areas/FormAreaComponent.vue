<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" label="Regional" placeholder="Seleccione la regional" :url="regionalsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_regional_id" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_sede" name="employee_headquarter_id" label="Sede" placeholder="Seleccione la sede" :url="headquartersDataUrl" :parameters="{regional: form.employee_regional_id }" :emptyAll="empty.headquarter" @updateEmpty="updateEmptyKey('headquarter')">
          </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_headquarter_id" class="col-md-6" v-model="form.employee_process_id" :error="form.errorsFor('employee_process_id')"  name="employee_process_id" label="Macroprocesos" placeholder="Seleccione los macroprocesos" :url="processesDataUrl" :parameters="{headquarter: form.employee_headquarter_id }" :emptyAll="empty.process" @updateEmpty="updateEmptyKey('process')" :multiple="true" :allowEmpty="true" :selected-object="form.multiselect_employee_process_id">
          </vue-ajax-advanced-select>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" v-if="!modal" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
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
    regionalsDataUrl: { type: String, default: "" },
    headquartersDataUrl: { type: String, default: "" },
    processesDataUrl: { type: String, default: "" },
    disableWacthSelectInCreated: { type: Boolean, default: false},
    modal: { type: Boolean, default: false },
    area: {
      default() {
        return {
            name: '',
            employee_regional_id: '',
            employee_headquarter_id: '',
            employee_process_id: ''
        };
      }
    }
  },
  watch: {
    area() {
      this.loading = false;
      this.form = Form.makeFrom(this.area, this.method);
    },
    'form.employee_regional_id'() {
      this.emptySelect('employee_process_id', 'process')
      this.emptySelect('employee_headquarter_id', 'headquarter')
    },
    'form.employee_headquarter_id'() {
      this.emptySelect('employee_process_id', 'process')
    },
    'form.employee_process_id'() {
      if (this.disableWacth)
        this.disableWacth = false
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.area, this.method),
      empty: {
        headquarter: false,
        process: false
      },
      disableWacth: this.disableWacthSelectInCreated,
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

          if (this.modal)
          {
            Object.assign(this.$data, this.$options.data.apply(this))
            this.$parent.$emit("closeModal")
          }
          else
            this.$router.push({ name: "administrative-areas" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
