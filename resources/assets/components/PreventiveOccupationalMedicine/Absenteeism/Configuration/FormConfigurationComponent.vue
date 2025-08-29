<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
        <vue-radio :checked="form.expired_absenteeism" class="col-md-12" v-model="form.expired_absenteeism" :options="siNo" name="expired_absenteeism" :error="form.errorsFor('expired_absenteeism')" label="¿Desea recibir notificación por vencimiento cercano de incapacidad?">
        </vue-radio>

        <vue-advanced-select v-if="form.expired_absenteeism == 'SI'" class="col-md-6" v-model="form.name_table_absenteeism" :error="form.errorsFor('name_table_absenteeism')" name="name_table_absenteeism" label="Tabla" placeholder="Seleccione la tabla" :options="tableOptions" :searchable="true">
            </vue-advanced-select>

        <vue-advanced-select v-if="form.name_table_absenteeism" class="col-md-6" v-model="form.name_column_fec_ini_absenteeism" :error="form.errorsFor('name_column_fec_ini_absenteeism')" name="name_column_fec_ini_absenteeism" label="Nombre de la columna donde se guarda el valor de la fecha inicial de la incapacidad del empleado" placeholder="Seleccione la columna" :options="columnsOptions"  :searchable="true">
        </vue-advanced-select>
        
        <vue-advanced-select v-if="form.name_table_absenteeism" class="col-md-6" v-model="form.name_column_fec_fin_absenteeism" :error="form.errorsFor('name_column_fec_ini_absenteeism')" name="name_column_fec_ini_absenteeism" label="Nombre de la columna donde se guarda el valor de la fecha final de la incapacidad del empleado" placeholder="Seleccione la columna" :options="columnsOptions"  :searchable="true">
        </vue-advanced-select>
        
        <vue-advanced-select v-if="form.name_table_absenteeism" class="col-md-6" v-model="form.name_column_employee_name_absenteeism" :error="form.errorsFor('name_column_fec_ini_absenteeism')" name="name_column_fec_ini_absenteeism" label="Nombre de la columna donde se guarda el nombre del empleado incapacitado" placeholder="Seleccione la columna" :options="columnsOptions"  :searchable="true">
        </vue-advanced-select>
        
        <vue-advanced-select v-if="form.name_table_absenteeism" class="col-md-6" v-model="form.name_column_employee_identification_absenteeism" :error="form.errorsFor('name_column_fec_ini_absenteeism')" name="name_column_fec_ini_absenteeism" label="Nombre de la columna donde se guarda el numero de identificación del empleado incapacitado" placeholder="Seleccione la columna" :options="columnsOptions"  :searchable="true">
        </vue-advanced-select>
        
        <vue-advanced-select v-if="form.name_table_absenteeism" class="col-md-6" v-model="form.name_column_cod_diag_absenteeism" :error="form.errorsFor('name_column_fec_ini_absenteeism')" name="name_column_fec_ini_absenteeism" label="Nombre de la columna donde se guarda el código del diagnostico de la incapacidad del empleado" placeholder="Seleccione la columna" :options="columnsOptions"  :searchable="true">
        </vue-advanced-select>
        
        <vue-advanced-select v-if="form.name_table_absenteeism" class="col-md-6" v-model="form.name_column_employee_description_diag_absenteeism" :error="form.errorsFor('name_column_fec_ini_absenteeism')" name="name_column_fec_ini_absenteeism" label="Nombre de la columna donde se guarda la descripción del código del diagnostico de la incapacidad del empleado" placeholder="Seleccione la columna" :options="columnsOptions"  :searchable="true">
        </vue-advanced-select>

        <vue-radio  v-if="form.expired_absenteeism == 'SI' && infoComplete" :checked="form.days_alert_expiration_date_absenteeism_90" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism_90" :options="siNo" name="days_alert_expiration_date_absenteeism_90" :error="form.errorsFor('days_alert_expiration_date_absenteeism_90')" label="¿Desea recibir alertas de empleados con incapacidades iguales o mayores a 90 dias?">
        </vue-radio>

        <vue-ajax-advanced-select v-if="form.days_alert_expiration_date_absenteeism_90 == 'SI' && infoComplete" class="col-md-12" v-model="form.users_notify_expired_absenteeism_expired_90" :selected-object="form.multiselect_90_user_id" name="users_notify_expired_absenteeism_expired_90" label="Usuarios a notificar el vencimiento de 90 dias" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_absenteeism_expired_90')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  

        <vue-radio  v-if="form.expired_absenteeism == 'SI' && infoComplete" :checked="form.days_alert_expiration_date_absenteeism_180" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism_180" :options="siNo" name="days_alert_expiration_date_absenteeism_180" :error="form.errorsFor('days_alert_expiration_date_absenteeism_180')" label="¿Desea recibir alertas de empleados con incapacidades iguales o mayores a 180 dias?">
        </vue-radio>

        <vue-ajax-advanced-select v-if="form.days_alert_expiration_date_absenteeism_180 == 'SI' && infoComplete" class="col-md-12" v-model="form.users_notify_expired_absenteeism_expired_180" :selected-object="form.multiselect_180_user_id" name="users_notify_expired_absenteeism_expired_180" label="Usuarios a notificar el vencimiento de 180 dias" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_absenteeism_expired_180')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>  

        <vue-radio  v-if="form.expired_absenteeism == 'SI' && infoComplete" :checked="form.days_alert_expiration_date_absenteeism_540" class="col-md-12" v-model="form.days_alert_expiration_date_absenteeism_540" :options="siNo" name="days_alert_expiration_date_absenteeism_540" :error="form.errorsFor('days_alert_expiration_date_absenteeism_540')" label="¿Desea recibir alertas de empleados con incapacidades iguales o mayores a 540 dias?">
        </vue-radio>

        <vue-ajax-advanced-select v-if="form.days_alert_expiration_date_absenteeism_540 == 'SI' && infoComplete" class="col-md-12" v-model="form.users_notify_expired_absenteeism_expired_540" :selected-object="form.multiselect_540_user_id" name="users_notify_expired_absenteeism_expired_540" label="Usuarios a notificar el vencimiento de 540 dias" placeholder="Seleccione uno o mas usuarios" :url="userDataUrl" :error="form.errorsFor('users_notify_expired_absenteeism_expired_540')" :multiple="true" :allowEmpty="true"> </vue-ajax-advanced-select>        
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  components: {
    VueInput,
    VueRadio,
    VueTextarea,
    VueAjaxAdvancedSelect,
    VueAdvancedSelect
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
          days_alert_expiration_date_absenteeism_540: '',
          name_table_absenteeism: '',
          name_column_fec_ini_absenteeism: '',
          name_column_fec_fin_absenteeism: '',
          name_column_employee_name_absenteeism: '',
          name_column_employee_identification_absenteeism: '',
          name_column_cod_diag_absenteeism: '',
          name_column_employee_description_diag_absenteeism: '',

        };
      }
    }
  },
  watch: {
    configuration() {
      this.loading = false;
      this.form = Form.makeFrom(this.configuration, this.method);
    },
    'form.name_table_absenteeism'() {
      if (this.form.name_table_absenteeism)
      {
        axios.post('/selects/absenteeism/tables/columns', {table: this.form.name_table_absenteeism})
        .then(response => {
            this.columnsOptions = response.data;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
      }
    }
  },
  computed: {
    infoComplete() {
      if (this.form.name_table_absenteeism && this.form.name_column_fec_ini_absenteeism && this.form.name_column_fec_fin_absenteeism && this.form.name_column_employee_name_absenteeism && this.form.name_column_employee_identification_absenteeism && this.form.name_column_cod_diag_absenteeism && this.form.name_column_employee_description_diag_absenteeism)
      {
        return true;        
      }
      else
        return false
    }
  },
  created(){
        this.fetchSelect('tableOptions', this.tablesDataUrl)
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.configuration, this.method),
      userDataUrl: '/selects/users',
      tablesDataUrl: '/selects/absenteeism/tables',
      columnsOptions: [],
      tableOptions: [],
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
    },
    fetchSelect(key, url)
		{
			GlobalMethods.getDataMultiselect(url)
			.then(response => {
        console.log(response)
				this[key] = response;
			})
			.catch(error => {
				Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
				this.$router.go(-1);
			});
		},
  }
};
</script>
