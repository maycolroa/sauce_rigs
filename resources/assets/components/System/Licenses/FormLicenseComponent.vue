<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.company_id" :error="form.errorsFor('company_id')" :selected-object="form.multiselect_company" name="company_id" label="Compañia" placeholder="Seleccione la compañia" :url="companiesDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>
    <b-form-row>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.started_at" label="Fecha Inicio" :full-month-name="true" placeholder="Seleccione la fecha inicio" :error="form.errorsFor('started_at')" name="started_at">
          </vue-datepicker>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.ended_at" label="Fecha Fin" :full-month-name="true" placeholder="Seleccione la fecha fin" :error="form.errorsFor('ended_at')" name="ended_at">
          </vue-datepicker>
    </b-form-row>
    <b-form-row>
      <vue-advanced-select-group :disabled="viewOnly" v-model="form.module_id" class="col-md-12" :options="modules" :limit="1000" :searchable="true" name="module_id" label="Aplicación \ Módulo" placeholder="Seleccione los módulos" text-block="Módulos que están disponible para esta licencia" :error="form.errorsFor('module_id')" :selected-object="form.multiselect_module" :multiple="true">
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

    <b-form-row>
      <vue-checkbox-simple v-if="isEdit || viewOnly" style="padding-top: 30px;" :disabled="viewOnly" class="col-md-6" v-model="form.freeze" label="¿Congelar?" :checked="form.freeze" name="freeze" checked-value="SI" unchecked-value="NO"></vue-checkbox-simple>
    </b-form-row>

    <b-form-row v-if="viewOnly">
      <div class="col-md-12">
        <h4 class="font-weight-bold mb-1">
          Historial de cambios realizados
        </h4>
        <div class="col-md">
          <vue-table
              configName="system-license-histories"
              :modelId="form.id ? form.id : -1"
              ></vue-table>
        </div>
      </div>
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
          available_days: ''
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
      usersOptions: '/selects/users'
    };
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
    }
  }
};
</script>
