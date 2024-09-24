<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row v-if="auth.can['contracts_r']">
      <vue-input :disabled="!auth.can['configurations_c']" class="col-md-12" v-model="form.days_alert_without_activity" label="Días de alerta para contratistas sin actividad" type="number" name="days_alert_without_activity" :error="form.errorsFor('days_alert_without_activity')" placeholder="1" min="1"></vue-input>
    </b-form-row>
    
    <b-form-row v-if="auth.can['contracts_r']">
        <vue-input :disabled="!auth.can['configurations_c']" class="col-md-12" v-model="form.days_alert_expiration_date_contract_file_upload" label="Días de alerta por fecha de vencimiento cercana para los archivos cargados en el modulo de contratistas" type="number" name="days_alert_expiration_date_contract_file_upload" :error="form.errorsFor('days_alert_expiration_date_contract_file_upload')" placeholder="1"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.validate_qualification_list_check" class="col-md-12" v-model="form.validate_qualification_list_check" :options="siNo" name="validate_qualification_list_check" :error="form.errorsFor('validate_qualification_list_check')" label="Validar calificaciones de lista de chequeo">
        </vue-radio>
    </b-form-row>

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.contracts_delete_file_upload" class="col-md-12" v-model="form.contracts_delete_file_upload" :options="siNo" name="contracts_delete_file_upload" :error="form.errorsFor('contracts_delete_file_upload')" label="¿Permitir eliminar archivos aprobados o rechazados?">
        </vue-radio>
    </b-form-row> 

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.contracts_use_proyect" class="col-md-12" v-model="form.contracts_use_proyect" :options="siNo" name="contracts_use_proyect" :error="form.errorsFor('contracts_use_proyect')" label="¿Desea usar proyectos?">
        </vue-radio>
    </b-form-row> 

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.company_there_integration_contract" class="col-md-12" v-model="form.company_there_integration_contract" :options="siNo" name="company_there_integration_contract" :error="form.errorsFor('company_there_integration_contract')" label="¿Desea usar integración?">
        </vue-radio>
    </b-form-row> 

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.contract_validate_inactive_employee" class="col-md-12" v-model="form.contract_validate_inactive_employee" :options="siNo" name="contract_validate_inactive_employee" :error="form.errorsFor('contract_validate_inactive_employee')" label="¿Se obliga motivo y archivo anexo para la inactivación de empleados?">
        </vue-radio>
    </b-form-row> 

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.contract_notify_file_expired" class="col-md-12" v-model="form.contract_notify_file_expired" :options="siNo" name="contract_notify_file_expired" :error="form.errorsFor('contract_notify_file_expired')" label="¿Desea notificar al contratante de los archivos proximos a vencer?">
        </vue-radio>
    </b-form-row> 

    <b-form-row v-if="form.contract_notify_file_expired == 'SI'">
      <vue-ajax-advanced-select class="col-md-12" v-model="form.contract_notify_file_expired_user" :selected-object="form.multiselect_user_id" name="contract_notify_file_expired_user" label="Usuarios a notificar el vencimiento" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('contract_notify_file_expired_user')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  
    </b-form-row> 

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading || (!auth.can['configurations_c'])" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect
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
          validate_qualification_list_check: '',
          contracts_delete_file_upload: '',
          contracts_use_proyect: '',
          company_there_integration_contract: '',
          contract_validate_inactive_employee: '',
          contract_notify_file_expired: '',
          contract_notify_file_expired_user: '',
          days_alert_expiration_date_contract_file_upload: '',
          days_alert_without_activity: ''
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
          window.location = "/legalaspects/contracts"
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
