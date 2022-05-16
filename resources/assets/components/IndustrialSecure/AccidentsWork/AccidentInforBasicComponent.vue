<template>
      <div>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="infor.nivel_accidente" class="col-md-12" v-model="infor.nivel_accidente" :options="accidentLevels" name="nivel_accidente" :error="form.errorsFor('nivel_accidente')" label="Nivel de accidente"></vue-radio>          
          </b-form-row>
          <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="infor.fecha_envio_arl" label="Fecha en que se envía la investigación a la ARL" :full-month-name="true" placeholder="Seleccione la fecha" :error="form.errorsFor('fecha_envio_arl')" name="fecha_envio_arl" :disabled-dates="disabledDates">
                </vue-datepicker>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="infor.fecha_envio_empresa" label="Fecha en que se envía recomendación a la empresa" :full-month-name="true" placeholder="Seleccione la fecha" :error="form.errorsFor('fecha_envio_empresa')" name="fecha_envio_empresa" :disabled-dates="disabledDates">
            </vue-datepicker>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="infor.coordinador_delegado" label="Coordinador delegado" type="text" name="coordinador_delegado" :error="form.errorsFor('coordinador_delegado')" placeholder="Coordinador delegado"></vue-input>   
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="infor.cargo" label="Cargo" type="text" name="cargo" :error="form.errorsFor('cargo')" placeholder="Cargo"></vue-input> 
          </b-form-row>
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="infor.employee_eps_id" :error="form.errorsFor('employee_eps_id')" :selected-object="infor.multiselect_eps" name="employee_eps_id" :label="keywordCheck('eps')+' a la que esta afiliado'" placeholder="Seleccione una opción" :url="epsDataUrl">
            </vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="infor.employee_afp_id" :error="form.errorsFor('employee_afp_id')" :selected-object="infor.multiselect_afp" name="employee_afp_id" :label="keywordCheck('afp')+' a la que esta afiliado'" placeholder="Seleccione una opción" :url="afpDataUrl">
            </vue-ajax-advanced-select>   
          </b-form-row>  

          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="infor.employee_arl_id" :error="form.errorsFor('employee_arl_id')" :selected-object="infor.multiselect_arl" name="employee_arl_id" :label="keywordCheck('arl')+' a la que esta afiliado'" placeholder="Seleccione una opción" :url="arlDataUrl">
            </vue-ajax-advanced-select>
            <vue-radio :disabled="viewOnly" :checked="infor.tiene_seguro_social" class="col-md-6" v-model="infor.tiene_seguro_social" :options="siNo" name="tiene_seguro_social" :error="form.errorsFor('tiene_seguro_social')" label="Seguro Social"/>
          </b-form-row>
          <b-form-row v-if="infor.tiene_seguro_social == 'SI'">
            <vue-input :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="infor.nombre_seguro_social" label="¿Cual?" type="text" name="nombre_seguro_social" :error="form.errorsFor('nombre_seguro_social')" placeholder="¿Cual?"></vue-input>
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
    infor: {
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
          nombre_actividad_economica_centro_trabajo: '',
          direccion_centro_trabajo: '',
          telefono_centro_trabajo: '',
          email_centro_trabajo: '',
          departamento_centro_trabajo_id: '',
          ciudad_centro_trabajo_id: '',
          zona_centro_trabajo: '',
        }
      }
    },
  },
  data() {
    return {
      form: Form.makeFrom(this.infor, this.method),      
      epsDataUrl: "/selects/eps",
      afpDataUrl: "/selects/afp",
      arlDataUrl: "/selects/arl",
      zones: [
         {text: 'Urbana', value: 'Urbana'},
         {text: 'Rural', value: 'Rural'}
      ],
      siNo: [
         {text: 'SI', value: 'SI'},
         {text: 'NO', value: 'NO'}
      ],
      typesDocuments: [
        {text: 'NI', value: 'NI'},
        {text: 'CC', value: 'CC'},
        {text: 'CE', value: 'CE'},
        {text: 'NU', value: 'NU'},
        {text: 'PA', value: 'PA'},
      ],
      accidentLevels: [
        {text: 'Accidente', value: 'Accidente'},
        {text: 'Accidente grave', value: 'Accidente grave'},
        {text: 'Accidente mortal', value: 'Accidente mortal'},
        {text: 'Accidente leve', value: 'Accidente leve'},
        {text: 'Incidente', value: 'Incidente'},
      ],
      disabledDates: {
        from: new Date()
      }
    }
  }
}
</script>
