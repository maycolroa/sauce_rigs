<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-ajax-advanced-select :disabled="!form.group_company" class="col-md-12" v-model="form.company_id" :error="form.errorsFor('company_id')" :selected-object="form.multiselect_company" name="company_id" label="Compañia" placeholder="Seleccione la compañia" :url="companiesDataUrl" :parameters="{group_id: form.group_company }">
          </vue-ajax-advanced-select>
    </b-form-row>
    <b-form-row>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.started_at" label="Fecha Inicio" :full-month-name="true" placeholder="Seleccione la fecha inicio" :error="form.errorsFor('started_at')" name="started_at" :disabled-dates="disabledDates()" @input="recalculateDateEndedAt()">
          </vue-datepicker>
      <vue-datepicker :disabled="true" class="col-md-6" v-model="form.ended_at" label="Fecha Fin" :full-month-name="true" placeholder="Seleccione la fecha fin" :error="form.errorsFor('ended_at')" name="ended_at">
          </vue-datepicker>
    </b-form-row>
    <b-form-row>
      <vue-advanced-select-group v-model="form.module_id" class="col-md-12" :options="modules" :searchable="true" name="module_id" label="Aplicación \ Módulo" placeholder="Seleccione los módulos" text-block="Módulos que están disponible para esta licencia" :error="form.errorsFor('module_id')" :selected-object="form.multiselect_module" :multiple="true" @input="countModules()">
          </vue-advanced-select-group>
    </b-form-row>

    <b-form-row>
        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.user_id" :selected-object="form.multiselect_user" name="user_id" label="Usuario" placeholder="Seleccione el usuario" :url="usersOptions" :error="form.errorsFor('user_id')">
            </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row v-if="!viewOnly">
      <vue-advanced-select class="col-md-12" v-model="form.add_email" name="add_email" :error="form.errorsFor(`add_email`)" label="Correos que seran notificados" placeholder="Selecione correos" :options="[]" :multiple="true" :allowEmpty="true" :taggable="true" :searchable="true" :limit="50">
      </vue-advanced-select>
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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAdvancedSelectGroup from "@/components/Inputs/VueAdvancedSelectGroup.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import Form from "@/utils/Form.js";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueAdvancedSelectGroup,
    VueAdvancedSelect,
    VueDatepicker,
    VueCheckboxSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    companiesDataUrl: { type: String, default: "" },
    UsersDataUrl: { type: String, default: "" },
    modules: {
      type: Array,
      default: function() {
        return [];
      }
    },
    license: {
      default() {
        return {
          started_at: '',
          ended_at: '',
          company_id: '',
          module_id: '',
          add_email: [],
          user_id: '',
          freeze: '',
          available_days: '',
          id_license: '',
          modules_quantity: ''
        };
      }
    }
  },
  watch: {
    license() {
      this.loading = false;
      this.form = Form.makeFrom(this.license, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.license, this.method),
      usersOptions: '/selects/users',
      activateEvent: false,
    };
  }, 
  mounted() {
    setTimeout(() => {
      this.$nextTick(() => {
        this.activateEvent = true;
      });
    }, 5000)
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "system-licenses" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    recalculateDateEndedAt()
    {
      if (this.form.id_license)
      {
          axios.post('/system/license/recalculateEndedat', {ini: this.form.started_at, days: this.form.available_days})
        .then(response => {
          console.log(response);
            this.form.ended_at = response.data;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            //this.$router.go(-1);
        });
      }
    },
    countModules()
    {
      if (this.activateEvent)
      {
        if (this.form.module_id.length > this.form.modules_quantity)
        {
          Alerts.error('Error', 'Solo puede seleccionar '+this.form.modules_quantity+' modulo(s)');
          this.form.module_id.pop()
        }
      }
    },
    disabledDates() {
      let toDate = new Date(this.form.started_at);

      return {
            to: new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())
        }
    },
  }
};
</script>
