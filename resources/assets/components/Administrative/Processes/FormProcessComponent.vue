<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.abbreviation" label="Abreviatura (Opcional)" type="text" name="abbreviation" :error="form.errorsFor('abbreviation')" placeholder="Abreviatura"></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.types" name="types" :error="form.errorsFor('types')" label="Macroproceso" placeholder="Seleccione los macroprocesos" :url="tagsTypesDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
        </vue-ajax-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regional')" placeholder="Seleccione una opción" :url="regionalsDataUrl">
          </vue-ajax-advanced-select>
       <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_regional_id" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')"  name="employee_headquarter_id" :label="keywordCheck('headquarters')" placeholder="Seleccione las opciones" :url="headquartersDataUrl" :parameters="{regional: form.employee_regional_id }" :emptyAll="empty.headquarter" @updateEmpty="updateEmptyKey('headquarter')" :multiple="true" :allowEmpty="true" :selected-object="form.multiselect_employee_headquarter_id">
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
    disableWacthSelectInCreated: { type: Boolean, default: false},
    modal: { type: Boolean, default: false },
    process: {
      default() {
        return {
            name: '',
            types: '',
            employee_regional_id: '',
            employee_headquarter_id: '',
            abbreviation: ''
        };
      }
    }
  },
  watch: {
    process() {
      this.loading = false;
      this.form = Form.makeFrom(this.process, this.method);
    },
    'form.employee_regional_id'() {
      this.emptySelect('employee_headquarter_id', 'headquarter')
    },
    'form.employee_headquarter_id'() {
      if (this.disableWacth)
        this.disableWacth = false
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.process, this.method),
      empty: {
        headquarter: false
      },
      disableWacth: this.disableWacthSelectInCreated,
      tagsTypesDataUrl: '/selects/tagsTypeProcess',
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
            this.$router.push({ name: "administrative-processes" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
