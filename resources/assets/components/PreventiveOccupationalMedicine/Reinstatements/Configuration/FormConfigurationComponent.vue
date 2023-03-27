<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-radio :checked="form.reports_opens_notify" class="col-md-12" v-model="form.reports_opens_notify" :options="siNo" name="reports_opens_notify" :error="form.errorsFor('reports_opens_notify')" label="¿Desea recibir notificación por reportes con muchos dias sin seguimiento?">
        </vue-radio>
      <vue-input v-if="form.reports_opens_notify == 'SI'" class="col-md-12" v-model="form.days_alert_expiration_report_notify" label="Número de dias para el envio de la notificación" type="number" name="days_alert_expiration_report_notify" :error="form.errorsFor('days_alert_expiration_report_notify')" placeholder="1"></vue-input>

      <vue-ajax-advanced-select v-if="form.reports_opens_notify == 'SI'" class="col-md-12" v-model="form.users_notify_expired_report" :selected-object="form.multiselect_user_id" name="users_notify_expired_report" label="Usuarios a notificar" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_report')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  

      <vue-radio :checked="form.reports_resumen_month" class="col-md-12" v-model="form.reports_resumen_month" :options="siNo" name="reports_resumen_month" :error="form.errorsFor('reports_resumen_month')" label="¿Desea recibir notificación mensual con el resumen de los eventos a cumplirse?">
        </vue-radio>
    </b-form-row>
    <b-form-row v-if="auth.company_id == 409">
      <vue-radio :checked="form.notify_incapacitated" class="col-md-12" v-model="form.notify_incapacitated" :options="siNo" name="notify_incapacitated" :error="form.errorsFor('notify_incapacitated')" label="¿Desea recibir notificación por tiempo de incapacidad?">
        </vue-radio>
      <vue-input v-if="form.notify_incapacitated == 'SI'" class="col-md-12" v-model="form.days_notify_incapacitated" label="Número de días de incapacidad para la primera notificación" type="number" name="days_notify_incapacitated" :error="form.errorsFor('days_notify_incapacitated')" placeholder="1"></vue-input>

      <vue-input v-if="form.notify_incapacitated == 'SI'" class="col-md-12" v-model="form.days_notify_incapacitated_2" label="Número de días de incapacidad para la segunda notificación (Este parametro es opcional)" type="number" name="days_notify_incapacitated_2" :error="form.errorsFor('days_notify_incapacitated_2')" placeholder="1"></vue-input>

      <vue-input v-if="form.notify_incapacitated == 'SI'" class="col-md-12" v-model="form.days_notify_incapacitated_3" label="Número de días de incapacidad para la tercera notificación (Este parametro es opcional)" type="number" name="days_notify_incapacitated_3" :error="form.errorsFor('days_notify_incapacitated_3')" placeholder="1"></vue-input>

      <vue-ajax-advanced-select v-if="form.notify_incapacitated == 'SI'" class="col-md-12" v-model="form.users_notify_incapacitated" :selected-object="form.multiselect_user_incapacitated_id" name="users_notify_incapacitated" label="Usuarios a notificar el vencimiento" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_incapacitated')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  
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
          users_notify_expired_report: '',
          reports_opens_notify: '',
          days_alert_expiration_report_notify: '',
          reports_resumen_month: '',
          users_notify_incapacitated: '',
          notify_incapacitated: '',
          days_notify_incapacitated: '',
          days_notify_incapacitated_2 : '',
          days_notify_incapacitated_3 : ''

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
          this.$router.push({ name: "preventiveoccupationalmedicine-reinstatements" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
