<template>
      <div>
          <b-form-row>
            <vue-input :disabled="true" class="col-md-12" v-model="company.nombre_actividad_economica_sede_principal" label="Nombre de la actividad económica" type="text" name="nombre_actividad_economica_sede_principal" :error="form.errorsFor('nombre_actividad_economica_sede_principal')" placeholder="Nombre de la actividad económica"></vue-input>
            <vue-input :disabled="true" class="col-md-12" v-model="company.razon_social" label="Nombre o razón social" type="text" name="razon_social" :error="form.errorsFor('razon_social')" placeholder="Nombre o razón social"></vue-input>            
          </b-form-row>
          <b-form-row>
            <vue-radio :disabled="true" :checked="company.tipo_identificacion_sede_principal" class="col-md-6" v-model="company.tipo_identificacion_sede_principal" :options="typesDocuments" name="tipo_identificacion_sede_principal" :error="form.errorsFor('tipo_identificacion_sede_principal')" label="Tipo de identificación"></vue-radio>
            <vue-input :disabled="true" class="col-md-6" v-model="company.identificacion_sede_principal" label="Número" type="text" name="identificacion_sede_principal" :error="form.errorsFor('identificacion_sede_principal')" placeholder="Documento"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="true" class="col-md-6" v-model="company.direccion_sede_principal" label="Dirección" type="text" name="direccion_sede_principal" :error="form.errorsFor('direccion_sede_principal')" placeholder="Dirección"></vue-input>
            <vue-input :disabled="true" class="col-md-6" v-model="company.email_sede_principal" label="Email" type="text" name="email_sede_principal" :error="form.errorsFor('email_sede_principal')" placeholder="Email"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="true" class="col-md-6" v-model="company.telefono_sede_principal" label="Teléfono" type="text" name="telefono_sede_principal" :error="form.errorsFor('telefono_sede_principal')" placeholder="Teléfono"></vue-input>
            <vue-ajax-advanced-select :disabled="true" class="col-md-6" v-model="company.departamento_sede_principal_id" :selected-object="company.multiselect_departament_sede" name="departamento_sede_principal_id" :error="form.errorsFor('departamento_sede_principal_id')" label="Departamento" placeholder="Seleccione el departamento" :url="departamentsUrl"></vue-ajax-advanced-select>
          </b-form-row>    
           <b-form-row>
            <vue-ajax-advanced-select :disabled="true" class="col-md-6" v-model="company.ciudad_sede_principal_id" :selected-object="company.multiselect_municipality_sede" name="ciudad_sede_principal_id" :error="form.errorsFor('ciudad_sede_principal_id')" label="Ciudad" placeholder="Seleccione la ciudad" :url="minicipalitiessUrl" :parameters="{departament: company.departamento_sede_principal_id }"></vue-ajax-advanced-select>
            <vue-radio :disabled="true" :checked="company.zona_sede_principal" class="col-md-6" v-model="company.zona_sede_principal" :options="zones" name="zona_sede_principal" :error="form.errorsFor('zona_sede_principal')" label="Zona"></vue-radio>
          </b-form-row>
          <b-form-row>
            <label class="col-md-6 offset-md-4">Centro de trabajo donde labora el trabajador</label>
            <vue-radio :disabled="viewOnly" :checked="company.info_sede_principal_misma_centro_trabajo" class="col-md-12" v-model="company.info_sede_principal_misma_centro_trabajo" :options="siNo" name="info_sede_principal_misma_centro_trabajo" :error="form.errorsFor('info_sede_principal_misma_centro_trabajo')" label="¿Son los datos del centro de trabajo los mismos de la sede principal?"></vue-radio>
          </b-form-row> 
          <b-form-row v-if="company.info_sede_principal_misma_centro_trabajo == 'NO'">            
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="company.centro_trabajo_secundary_id" :selected-object="company.multiselect_center" name="centro_trabajo_secundary_id" :error="form.errorsFor('centro_trabajo_secundary_id')" label="Centro de trabajo del empleado" placeholder="Seleccione el centro" :url="centersUrl"></vue-ajax-advanced-select>
          </b-form-row>
      </div>
</template>


<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
  },
  props:{
    viewOnly: { type: Boolean, default: false },
    isEdit: { type: Boolean, default: false },
    form: { type: Object, required: true },
    company: {
      default() {
        return {
          razon_social: '',
          nombre_actividad_economica_sede_principal: '',
          tipo_identificacion_sede_principal: '',
          identificacion_sede_principal: '',
          direccion_sede_principal: '',
          telefono_sede_principal: '',
          email_sede_principal: '',
          departamento_sede_principal_id: '',
          ciudad_sede_principal_id: '',
          zona_sede_principal: '',
          info_sede_principal_misma_centro_trabajo: '',
          centro_trabajo_secundary_id: ''
        }
      }
    },
  },
  watch: {
    company() {
      this.loading = false;
      this.$emit('input', this.company);
    }
  },
  data() {
    return {
      departamentsUrl: '/selects/departaments',
      minicipalitiessUrl: '/selects/municipalities',
      centersUrl: '/selects/centers',
      zones: [
         {text: 'Urbana', value: 'Urbana'},
         {text: 'Rural', value: 'Rural'}
      ],
      siNo: [
         {text: 'SI', value: 'SI'},
         {text: 'NO', value: 'NO'}
      ],
      typesDocuments: [
        {text: 'NIT', value: 'NIT'},
        {text: 'CC', value: 'CC'},
        {text: 'CE', value: 'CE'},
        {text: 'NU', value: 'NU'},
        {text: 'PA', value: 'PA'},
      ]
    }
  }
}
</script>
