<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-input v-if="!isEdit || auth.hasRole['Superadmin']" help-text="La contraseña debe contener mínimo 8 caracteres entre los cuales debe haber al menos un número y un caracter especial (@$!%?&*._-). Es un campo opcional. En caso de no ingresar la contraseña se le enviará un correo al usuario para que sea él quien la configure." :disabled="viewOnly" class="col-md-6" v-model="form.password" label="Contraseña" type="password" name="password" :error="form.errorsFor('password')" placeholder="Contraseña"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.document" label="Documento" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
    </b-form-row>

    <b-form-row>
      <template v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista'] && form.edit_role">
        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.role_id" :error="form.errorsFor('role_id')" :selected-object="form.multiselect_role" name="role_id" label="Rol" placeholder="Seleccione el rol del usuario" :url="rolesDataUrl" :multiple="true">
            </vue-ajax-advanced-select>
      </template>
    </b-form-row>
    <b-form-row>
      <vue-checkbox-simple v-if="isEdit || viewOnly" style="padding-top: 30px;" :disabled="viewOnly" class="col-md-6" v-model="form.active" label="¿Activo?" :checked="form.active" name="active" checked-value="SI" unchecked-value="NO"></vue-checkbox-simple>
    </b-form-row>

    <template v-if="Object.keys(filtersConfig).length > 0 && form.role_id">
      <b-form-row v-if="filterReinstatements">
        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.filter_headquarter" :error="form.errorsFor('filter_headquarter')" :selected-object="form.multiselect_filter_headquarter" name="filter_headquarter" label="Sedes (Opcional)" placeholder="Seleccione las sedes" :url="headquartersDataUrl" :allowEmpty="true" :multiple="true" text-block="Sedes mediante las cuales sera filtrada la información que podra visualizar el usuario en el módulo de Reincorporaciones">
            </vue-ajax-advanced-select>
      </b-form-row>
      <b-form-row v-if="filterLegalMatrix">
        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.filter_system_apply" :error="form.errorsFor('filter_system_apply')" :selected-object="form.multiselect_filter_system_apply" name="filter_system_apply" label="Sistemas que aplican (Opcional)" placeholder="Seleccione los sistemas" :url="systemApplyDataUrl" :allowEmpty="true" :multiple="true" text-block="Sistemas que aplican mediante los cuales sera filtrada la información que podra visualizar el usuario en el módulo de Matriz Legal">
            </vue-ajax-advanced-select>
      </b-form-row>

      <b-form-row v-if="filterInspections">
        <vue-ajax-advanced-select v-if="locationFormUser.regional == 'SI' && auth.filter_inspections == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regionals')" :parameters="{form: 'inspections' }" placeholder="Seleccione las opciones" :url="regionalsDataUrl" :multiple="true" :allowEmpty="true">
            </vue-ajax-advanced-select>
        <vue-ajax-advanced-select v-if="locationFormUser.headquarter == 'SI' && auth.filter_inspections == 'SI'" :disabled="viewOnly || (form.employee_regional_id && form.employee_regional_id.length == 0)" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_sede" name="employee_headquarter_id" :label="keywordCheck('headquarters')" placeholder="Seleccione las opciones" :url="headquartersDataUrl2" :parameters="{regional: form.employee_regional_id, form: 'inspections' }" :multiple="true" :allowEmpty="true">
            </vue-ajax-advanced-select>
        
        <vue-ajax-advanced-select v-if="locationFormUser.process == 'SI' && auth.filter_inspections == 'SI'" :disabled="viewOnly || (form.employee_headquarter_id &&form.employee_headquarter_id.length == 0)" class="col-md-6" v-model="form.employee_process_id" :error="form.errorsFor('employee_process_id')" :selected-object="form.multiselect_proceso" name="employee_process_id" :label="keywordCheck('processes')" placeholder="Seleccione las opciones" :url="processesDataUrl" :parameters="{headquarter: form.employee_headquarter_id, form: 'inspections' }" :multiple="true" :allowEmpty="true">
        </vue-ajax-advanced-select>
        <vue-ajax-advanced-select v-if="locationFormUser.area == 'SI' && auth.filter_inspections == 'SI'" :disabled="viewOnly || (form.employee_process_id && form.employee_process_id.length == 0)" class="col-md-6" v-model="form.employee_area_id" :error="form.errorsFor('employee_area_id')" :selected-object="form.multiselect_area" name="employee_area_id" :label="keywordCheck('areas')" placeholder="Seleccione las opciones" :url="areasDataUrl" :parameters="{process: form.employee_process_id, headquarter: form.employee_headquarter_id, form: 'inspections' }" :multiple="true" :allowEmpty="true">
        </vue-ajax-advanced-select>
      </b-form-row>
    </template>

    <template v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']" v-show="contracts.length > 0">
      <div style= "margin-bottom: 20px;">
        <h6><b>Contratistas a los cuales pertenece el usuario</b></h6>
        <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
             <tbody>
                <tr v-for="(contract, index) in contracts" :key="contract.id">
                    <td style='text-center align-middle'>
                        {{ index + 1 }} . {{ contract.social_reason }}
                    </td>
                </tr>
            </tbody>
        </table>
      </div>
    </template>

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
import VueInput from "@/components/Inputs/VueInput.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Form from "@/utils/Form.js";
//import LocationLevelComponent from '@/components/CustomInputs/LocationLevelUserComponent.vue';

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput,
    VueCheckboxSimple,
   // LocationLevelComponent
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    rolesDataUrl: { type: String, default: "" },
    headquartersDataUrl: { type: String, default: "" },
    systemApplyDataUrl: { type: String, default: "" },
    filtersConfig: { type: Object, default() {
        return {};
    }},
    contracts: {
			type: Array,
			default: function() {
				return [];
			}
		},
    user: {
      default() {
        return {
            name: '',
            email: '',
            password: '',
            document: '',
            role_id: [],
            edit_role: true,
            filter_headquarter: [],
            filter_system_apply: [],
            employee_regional_id: [],
            employee_headquarter_id: [],
            employee_area_id: [],
            employee_process_id: [],
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
  computed: {
    filterReinstatements() {
      return this.showFilter('reinstatements');
    },
    filterLegalMatrix() {
      return this.showFilter('legalMatrix');
    },
    filterInspections() {
      return this.showFilter('dangerousConditions');
    }
  },
  created()
  {
      axios.post('/administration/configurations/locationLevelForms/getConfUser')
      .then(data => {
          console.log(data.data)
          if (Object.keys(data.data).length > 0)
              setTimeout(() => {
                  this.locationFormUser = data.data
              }, 3000)
          this.isLoading = false;
      })
      .catch(error => {
          this.isLoading = false;
          Alerts.error('Error', 'Hubo un problema recolectando la información');
      });
  },
  data() {
    return {
      loading: false,//this.isEdit,
      form: Form.makeFrom(this.user, this.method),
      regionalsDataUrl: '/selects/regionals',
      headquartersDataUrl2: '/selects/headquarters',
      areasDataUrl: '/selects/areas',
      processesDataUrl: '/selects/processes',
      locationFormUser: []
    };
  },
  methods: {
    showFilter(search) {
      let show = false;

      if (this.form.role_id != undefined && this.form.role_id.length > 0)
      {
        _.forIn(this.form.role_id, (value, key) => {
          if (this.filtersConfig[value.value][search] == 'SI') {
            show = true;
            return show;
          }
        });
      }

      return show;
    },
    submit(e) {

      if (Object.keys(this.filtersConfig).length > 0 && this.form.role_id)
      {
        if (!this.filterReinstatements)
          this.form.filter_headquarter.splice(0)

        if (!this.filterLegalMatrix)
          this.form.filter_system_apply.splice(0)
      }
 
      console.log(this.form)

      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "administrative-users" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
