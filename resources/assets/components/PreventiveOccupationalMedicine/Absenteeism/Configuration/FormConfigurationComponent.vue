<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-radio :checked="form.expired_absenteeism" class="col-md-12" v-model="form.expired_absenteeism" :options="siNo" name="expired_absenteeism" :error="form.errorsFor('expired_absenteeism')" label="¿Desea recibir notificación por vencimiento cercano de incapacidad?">
        </vue-radio>
      <vue-input v-if="form.expired_absenteeism == 'SI'" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism" label="Días de incapacidad para la primera alerta" type="number" name="days_alert_expiration_date_absenteeism" :error="form.errorsFor('days_alert_expiration_date_absenteeism')" placeholder="1"></vue-input>

      <vue-input v-if="form.expired_absenteeism == 'SI'" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism_2" label="Días de incapacidad para la segunda alerta (Este parametro es opcional)" type="number" name="days_alert_expiration_date_absenteeism_2" :error="form.errorsFor('days_alert_expiration_date_absenteeism_2')" placeholder="1"></vue-input>

      <vue-ajax-advanced-select v-if="form.expired_absenteeism == 'SI'" class="col-md-12" v-model="form.users_notify_expired_absenteeism_expired" :selected-object="form.multiselect_user_id" name="users_notify_expired_absenteeism_expired" label="Usuarios a notificar el vencimiento" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_absenteeism_expired')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  
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
          users_notify_expired_absenteeism_expired: '',
          expired_absenteeism: '',
          days_alert_expiration_date_absenteeism: '',
          days_alert_expiration_date_absenteeism_2 : ''
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
