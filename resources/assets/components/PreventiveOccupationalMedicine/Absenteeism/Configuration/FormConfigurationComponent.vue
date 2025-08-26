<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
        <vue-radio :checked="form.expired_absenteeism" class="col-md-12" v-model="form.expired_absenteeism" :options="siNo" name="expired_absenteeism" :error="form.errorsFor('expired_absenteeism')" label="¿Desea recibir notificación por vencimiento cercano de incapacidad?">
        </vue-radio>

        
        <vue-input v-if="form.expired_absenteeism == 'SI'" class="col-md-12" v-model="form.name_table_absenteeism" label="Nombre de la tabla a analizar para el calculo de las alertas" type="text" name="name_table_absenteeism" :error="form.errorsFor('name_table_absenteeism')" help-text="Ejemplo: tabla_empleados_incapacidades"></vue-input>
        
        <vue-input v-if="form.expired_absenteeism == 'SI'" class="col-md-12" v-model="form.name_column_fec_ini_absenteeism" label="Nombre de la columna donde se guarda el valor de la fecha inicial de la incapacidad del empleado" type="text" name="name_column_fec_ini_absenteeism" :error="form.errorsFor('name_column_fec_ini_absenteeism')" help-text="Ejemplo: fecha_ini"></vue-input>
        
        <vue-input v-if="form.expired_absenteeism == 'SI'" class="col-md-12" v-model="form.name_column_fec_fin_absenteeism" label="Nombre de la columna donde se guarda el valor de la fecha inicial de la incapacidad del empleado" type="text" name="name_column_fec_fin_absenteeism" :error="form.errorsFor('name_column_fec_fin_absenteeism')" help-text="Ejemplo: fecha_fin"></vue-input>

        <vue-radio  v-if="form.expired_absenteeism == 'SI'" :checked="form.days_alert_expiration_date_absenteeism_90" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism_90" :options="siNo" name="days_alert_expiration_date_absenteeism_90" :error="form.errorsFor('days_alert_expiration_date_absenteeism_90')" label="¿Desea recibir alertas de empleados con incapacidades iguales o mayores a 90 dias?">
        </vue-radio>

        <vue-ajax-advanced-select v-if="form.days_alert_expiration_date_absenteeism_90 == 'SI'" class="col-md-12" v-model="form.users_notify_expired_absenteeism_expired_90" :selected-object="form.multiselect_90_user_id" name="users_notify_expired_absenteeism_expired_90" label="Usuarios a notificar el vencimiento de 90 dias" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_absenteeism_expired_90')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  

        <vue-radio  v-if="form.expired_absenteeism == 'SI'" :checked="form.days_alert_expiration_date_absenteeism_180" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism_180" :options="siNo" name="days_alert_expiration_date_absenteeism_180" :error="form.errorsFor('days_alert_expiration_date_absenteeism_180')" label="¿Desea recibir alertas de empleados con incapacidades iguales o mayores a 180 dias?">
        </vue-radio>

        <vue-ajax-advanced-select v-if="form.days_alert_expiration_date_absenteeism_180 == 'SI'" class="col-md-12" v-model="form.users_notify_expired_absenteeism_expired_180" :selected-object="form.multiselect_180_user_id" name="users_notify_expired_absenteeism_expired_180" label="Usuarios a notificar el vencimiento de 180 dias" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_absenteeism_expired_180')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  

        <vue-radio  v-if="form.expired_absenteeism == 'SI'" :checked="form.days_alert_expiration_date_absenteeism_540" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism_540" :options="siNo" name="days_alert_expiration_date_absenteeism_540" :error="form.errorsFor('days_alert_expiration_date_absenteeism_540')" label="¿Desea recibir alertas de empleados con incapacidades iguales o mayores a 540 dias?">
        </vue-radio>

        <vue-ajax-advanced-select v-if="form.days_alert_expiration_date_absenteeism_540 == 'SI'" class="col-md-12" v-model="form.users_notify_expired_absenteeism_expired_540" :selected-object="form.multiselect_540_user_id" name="users_notify_expired_absenteeism_expired_540" label="Usuarios a notificar el vencimiento de 540 dias" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_absenteeism_expired_540')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>        
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading || (!auth.can['absen_config_r'])" variant="primary">Guardar</b-btn>
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

export default {
  components: {
    VueInput,
    VueRadio,
    VueTextarea,
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
          users_notify_expired_absenteeism_expired_90: '',
          users_notify_expired_absenteeism_expired_180: '',
          users_notify_expired_absenteeism_expired_540: '',
          expired_absenteeism: '',
          days_alert_expiration_date_absenteeism_90: '',
          days_alert_expiration_date_absenteeism_180: '',
          days_alert_expiration_date_absenteeism_540: ''
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
          this.$router.push({ name: "preventiveoccupationalmedicine-absenteeism" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
