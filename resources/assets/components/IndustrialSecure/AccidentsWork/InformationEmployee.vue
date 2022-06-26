<template>
      <div>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.nombre_persona" label="Nombre" type="text" name="nombre_persona" :error="form.errorsFor('nombre_persona')" placeholder="Nombre"></vue-input>
            <vue-radio :disabled="viewOnly" :checked="employee.tipo_identificacion_persona" class="col-md-6" v-model="employee.tipo_identificacion_persona" :options="typesDocuments" name="tipo_identificacion_persona" :error="form.errorsFor('tipo_identificacion_persona')" label="Tipo de identificación"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.identificacion_persona" label="Número de identificación" type="text" name="identificacion_persona" :error="form.errorsFor('identificacion_persona')" placeholder="Documento"></vue-input>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="employee.fecha_nacimiento_persona" label="Fecha de nacimiento" :full-month-name="true" placeholder="Seleccione la fecha de nacimiento" :error="form.errorsFor('fecha_nacimiento_persona')" name="fecha_nacimiento_persona" :disabled-dates="disabledDates">
                </vue-datepicker>
          </b-form-row>
          <b-form-row>
            <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.sexo_persona" :error="form.errorsFor('sexo_persona')" :multiple="false" :options="sexs" :hide-selected="false" name="sexo_persona" label="Sexo" placeholder="Seleccione el sexo">
                </vue-advanced-select>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.email_persona" label="Email" type="text" name="email_persona" :error="form.errorsFor('email_persona')" placeholder="Email"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.telefono_persona" label="Teléfono" type="text" name="telefono_persona" :error="form.errorsFor('telefono_persona')" placeholder="Teléfono"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.cargo_persona" label="Cargo" type="text" name="cargo_persona" :error="form.errorsFor('cargo_persona')" placeholder="Cargo"></vue-input>
          </b-form-row>
          <b-form-row>            
            <vue-radio :disabled="viewOnly" :checked="employee.tipo_vinculacion_persona" class="col-md-12" v-model="employee.tipo_vinculacion_persona" :options="personLinkingTypes" name="tipo_vinculacion_persona" :error="form.errorsFor('tipo_vinculacion_persona')" label="Tipo de vinculación"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.direccion_persona" label="Dirección" type="text" name="direccion_persona" :error="form.errorsFor('direccion_persona')" placeholder="Dirección"></vue-input>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.departamento_persona_id" :selected-object="employee.multiselect_departamento_persona" name="departamento_persona_id" :error="form.errorsFor('departamento_persona_id')" label="Departamento" placeholder="Seleccione el departamento" :url="departamentsUrl"></vue-ajax-advanced-select>
          </b-form-row>    
           <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.ciudad_persona_id" :selected-object="employee.multiselect_ciudad_persona" name="ciudad_persona_id" :error="form.errorsFor('ciudad_persona_id')" label="Ciudad" placeholder="Seleccione la ciudad" :url="minicipalitiessUrl" :parameters="{departament: employee.departamento_persona_id }"></vue-ajax-advanced-select>
            <vue-radio :disabled="viewOnly" :checked="employee.zona_persona" class="col-md-6" v-model="employee.zona_persona" :options="zones" name="zona_persona" :error="form.errorsFor('zona_persona')" label="Zona"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="employee.fecha_ingreso_empresa_persona" label="Fecha de ingreso a la empresa" :full-month-name="true" placeholder="Seleccione la fecha de nacimiento" :error="form.errorsFor('fecha_ingreso_empresa_persona')" name="fecha_ingreso_empresa_persona" :disabled-dates="disabledDates">
                </vue-datepicker>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.tiempo_ocupacion_habitual_persona" label="Tiempo de ocupacion habitual al momento del accidente" type="text" name="tiempo_ocupacion_habitual_persona" :error="form.errorsFor('tiempo_ocupacion_habitual_persona')" placeholder="Ej 5:40" help-text="Coloque el numero de horas seguido del numero de minutos separandolos con dos puntos"></vue-input>
          </b-form-row> 
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.salario_persona" label="Salario" type="number" name="salario_persona" :error="form.errorsFor('salario_persona')" placeholder="Salario"></vue-input>
            <vue-radio :disabled="viewOnly" :checked="employee.jornada_trabajo_habitual_persona" class="col-md-6" v-model="employee.jornada_trabajo_habitual_persona" :options="shifts" name="jornada_trabajo_habitual_persona" :error="form.errorsFor('jornada_trabajo_habitual_persona')" label="Jorada de trabajo habitual"></vue-radio>
          </b-form-row> 
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.employee_eps_id" :error="form.errorsFor('employee_eps_id')" :selected-object="employee.multiselect_eps" name="employee_eps_id" :label="keywordCheck('eps')+' a la que está afiliado'" placeholder="Seleccione una opción" :url="epsDataUrl">
            </vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.employee_afp_id" :error="form.errorsFor('employee_afp_id')" :selected-object="employee.multiselect_afp" name="employee_afp_id" :label="keywordCheck('afp')+' a la que está afiliado'" placeholder="Seleccione una opción" :url="afpDataUrl">
            </vue-ajax-advanced-select>   
          </b-form-row>  
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.employee_arl_id" :error="form.errorsFor('employee_arl_id')" :selected-object="employee.multiselect_arl" name="employee_arl_id" :label="keywordCheck('arl')+' a la que está afiliado'" placeholder="Seleccione una opción" :url="arlDataUrl">
            </vue-ajax-advanced-select>
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
    sexs: {
      type: Array,
      default: function() {
        return [];
      }
    },
    viewOnly: { type: Boolean, default: false },
    form: { type: Object, required: true },
    isEdit: { type: Boolean, default: false },
    employee: {
      default() {
        return {
          tipo_vinculacion_persona: '',
          nombre_persona: '',
          tipo_identificacion_persona: '',
          identificacion_persona: '',
          fecha_nacimiento_persona: '',
          sexo_persona: '',
          direccion_persona: '',
          telefono_persona: '',
          email_persona: '',
          departamento_persona_id: '',
          ciudad_persona_id: '',
          zona_persona: '',
          cargo_persona: '',
          tiempo_ocupacion_habitual_persona: '',
          fecha_ingreso_empresa_persona: '',
          salario_persona: '',
          jornada_trabajo_habitual_persona: '',
          employee_eps_id: '',
          employee_arl_id: '',
          employee_afp_id: '',
        }
      }
    },
  },
  watch: {
    employee() {
      this.loading = false;
      this.$emit('input', this.employee);
    }
  },
  data() {
    return {
      epsDataUrl: "/selects/eps",
      afpDataUrl: "/selects/afp",
      arlDataUrl: "/selects/arl",
      departamentsUrl: '/selects/departaments',
      minicipalitiessUrl: '/selects/municipalities',
      zones: [
         {text: 'Urbana', value: 'Urbana'},
         {text: 'Rural', value: 'Rural'}
      ],
      shifts: [
        {text: 'Diurna', value: 'Diurna'},
        {text: 'Nocturna', value: 'Nocturna'},
        {text: 'Mixto', value: 'Mixto'},
        {text: 'Turnos', value: 'Turnos'}
      ],
      personLinkingTypes: [
        {text: 'Planta', value: 'Planta'},
        {text: 'Misión', value: 'Misión'},
        {text: 'Cooperado', value: 'Cooperado'},
        {text: 'Estudiante o Aprendiz', value: 'Estudiante o Aprendiz'},
        {text: 'Independiente', value: 'Independiente'}
      ],
      typesDocuments: [
        {text: 'NI', value: 'NI'},
        {text: 'CC', value: 'CC'},
        {text: 'CE', value: 'CE'},
        {text: 'NU', value: 'NU'},
        {text: 'PA', value: 'PA'},
      ],
      disabledDates: {
        from: new Date()
      }
    }
  }
}
</script>
