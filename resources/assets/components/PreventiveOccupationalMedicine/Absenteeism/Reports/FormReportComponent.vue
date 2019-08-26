<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name_show" label="Nombre para mostrar" type="text" name="name_show" :error="form.errorsFor('name_show')"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name_report" label="Nombre del reporte" type="text" name="name_report" :error="form.errorsFor('name_report')"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.user" label="Usuario" type="text" name="user" :error="form.errorsFor('user')"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.site" label="Sitio" type="text" name="site" :error="form.errorsFor('site')"></vue-input>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.user_id" :selected-object="form.multiselect_user_id" name="user_id" label="Usuarios" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('user_id')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>      
      <vue-checkbox-simple :disabled="viewOnly" class="col-md-12" v-model="form.state" label="¿Activo?" :checked="form.state" name="state" checked-value=1 unchecked-value=0></vue-checkbox-simple>
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
import VueInput from "@/components/Inputs/VueInput.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueCheckboxSimple,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    report: {
      default() {
        return {
            name_show: '',
            name_report: '',
            site: '',
            user: '',
            state: 1,
            user_id:'',
        };
      }
    },
  },
  watch: {
    report() {
      this.loading = false;
      this.form = Form.makeFrom(this.report, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.report, this.method),
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
          this.$router.push({ name: "absenteeism-reports" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
