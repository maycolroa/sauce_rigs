<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>      
      <vue-ajax-advanced-select class="col-md-12" v-model="form.users_notify_report_license" :selected-object="form.multiselect_user_id" name="users_notify_report_license" label="Usuarios a notificar" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_report_license')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  

      <vue-datepicker-range class="col-md-12" v-model="form.filters_date_license" label="Fecha a filtrar" placeholder="Seleccione la fecha" name="filters_date_license" :error="form.errorsFor('filters_date_license')"></vue-datepicker-range> 
    </b-form-row>
    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";
import VueDatepickerRange from "@/components/Inputs/VueDatepickerRange.vue";

export default {
  components: {
    VueInput,
    VueRadio,
    VueTextarea,
    VueAjaxAdvancedSelect,
    VueDatepickerRange
  },
  props: {
    url: { type: String },
    method: { type: String },
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    configuration: {
      default() {
        return {
          users_notify_report_license: '',
          filters_date_license: '',

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
      form: Form.makeFrom(this.configuration, this.method),
      userDataUrl: '/selects/users'
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "system-licenses-report" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
