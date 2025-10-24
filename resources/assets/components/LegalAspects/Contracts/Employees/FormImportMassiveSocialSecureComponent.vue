<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="with-elements">
       <b-btn v-if="auth.can['contracts_employee_c']" variant="primary" href="/templates/employeesocialsecureimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>&nbsp;&nbsp;
      </b-card-header>
      <b-card-body>
        <b-form-row>
          <vue-textarea :disabled="false" class="col-md-3" v-model="form.description" label="Descripción" name="description" placeholder="Descripción" rows="2" :error="form.errorsFor('description')"></vue-textarea>
          <vue-advanced-select v-if="auth.company_id != 159" class="col-md-6" v-model="form.month_pay" :disabled="false" name="month_pay" label="Mes de pago" placeholder="Seleccione el mes de pago" :options="months" :error="form.errorsFor('month_pay')" :multiple="false" :allow-empty="false" help-text="El mes es opcional, de no seleccionar ninguno, se tomara el mes actual para realizar el calculo.">
          </vue-advanced-select>
        </b-form-row>
        <b-form-row>
          <vue-file-simple class="col-md-12" v-model="form.file_social_secure" label="Archivo de Seguridad Social" name="file_social_secure" placeholder="Seleccione un archivo" :error="form.errorsFor(`file_social_secure`)" :maxFileSize="20"/>
        </b-form-row>

        <b-form-row>
          <vue-file-simple class="col-md-12" v-model="form.file_employee" label="Archivo Data Empleado" name="file_employee" placeholder="Seleccione un archivo" :error="form.errorsFor(`file_employee`)" :maxFileSize="20"/>
        </b-form-row>
      </b-card-body>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Importar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";

export default {
  components: {
    PerfectScrollbar,
    VueFileSimple,
    VueTextarea,
    VueAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    user: {
      default() {
        return {
          file_social_secure: '',
          file_employee: '',
          description: '',
          month_pay: ''
        };
      }
    }
  },
  watch: {
    user() {
      this.loading = false;
      this.form = Form.makeFrom(this.user, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.user, this.method),
      months: [
          {name: 'Enero', value: '1'},
          {name: 'Febrero', value: '2'},
          {name: 'Marzo', value: '3'},
          {name: 'Abril', value: '4'},
          {name: 'Mayo', value: '5'},
          {name: 'Junio', value: '6'},
          {name: 'Julio', value: '7'},
          {name: 'Agosto', value: '8'},
          {name: 'Septiembre', value: '9'},
          {name: 'Octubre', value: '10'},
          {name: 'Noviembre', value: '11'},
          {name: 'Diciembre', value: '12'},
        ]
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
          this.$router.push({ name: "legalaspects-contracts-employees" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
}
</script>
