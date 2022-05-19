<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="Información General" class="mb-3 box-shadow-none">
          <b-row>
              <b-col>
                  <div><b>Identificación:</b> {{ employeeDetail.identification }}</div>
                  <div><b>Nombre:</b> {{ employeeDetail.name }}</div>
                  <div><b>Fecha de nacimiento:</b> {{ dateBirth }}</div>
                  <div><b>Sexo:</b> {{ employeeDetail.sex }}</div>
                  <div><b>Fecha de ingreso:</b> {{ incomeDate }}</div>
                  <div><b>Antigüedad:</b> {{ employeeDetail.antiquity }}</div>
                  <div><b>Edad:</b> {{ employeeDetail.age }}</div>
              </b-col>
              <b-col>
                  <div><b>{{ keywordCheck('position') }}:</b> {{ employeeDetail.position ? employeeDetail.position.name : '' }}</div>
                  <div><b>{{ keywordCheck('businesses') }}:</b> {{ employeeDetail.business ? employeeDetail.business.name : '' }}</div>
                  <div><b>{{ keywordCheck('regional') }}:</b> {{ employeeDetail.regional ? employeeDetail.regional.name : ''}}</div>
                  <div><b>{{ keywordCheck('headquarter') }}:</b> {{ employeeDetail.headquarter ? employeeDetail.headquarter.name : '' }}</div>
                  <div><b>{{ keywordCheck('process') }}:</b> {{ employeeDetail.process ? employeeDetail.process.name : '' }}</div>
                  <div v-if="employeeDetail.area"><b>{{ keywordCheck('area') }}:</b> {{ employeeDetail.area.name }}</div>
                  <div><b>{{ keywordCheck('eps') }}:</b> {{ employeeDetail.eps ? `${employeeDetail.eps.code} - ${employeeDetail.eps.name}` : '' }}</div>
                  <div><b>{{ keywordCheck('afp') }}:</b> {{ employeeDetail.afp ? `${employeeDetail.afp.code} - ${employeeDetail.afp.name}` : '' }}</div>
                  <div><b>{{ keywordCheck('arl') }}:</b> {{ employeeDetail.arl ? `${employeeDetail.arl.code} - ${employeeDetail.arl.name}` : '' }}</div>
              </b-col>
          </b-row>
        </b-card>
          <br><br>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="employee.tipo_vinculacion_persona" class="col-md-12" v-model="form.tipo_vinculacion_persona" :options="personLinkingTypes" name="tipo_vinculacion_persona" :error="form.errorsFor('tipo_vinculacion_persona')" label="Tipo de vinculación"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.direccion_persona" label="Dirección" type="text" name="direccion_persona" :error="form.errorsFor('direccion_persona')" placeholder="Dirección"></vue-input>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.departamento_persona_id" :selected-object="employee.multiselect_departament" name="departamento_persona_id" :error="form.errorsFor('departamento_persona_id')" label="Departamento" placeholder="Seleccione el departamento" :url="departamentsUrl"></vue-ajax-advanced-select>
          </b-form-row>    
           <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="employee.ciudad_persona_id" :selected-object="employee.multiselect_municipality" name="ciudad_persona_id" :error="form.errorsFor('ciudad_persona_id')" label="Ciudad" placeholder="Seleccione la ciudad" :url="minicipalitiessUrl" :parameters="{departament: employee.departamento_persona_id }"></vue-ajax-advanced-select>
            <vue-radio :disabled="viewOnly" :checked="employee.zona_persona" class="col-md-6" v-model="form.zona_persona" :options="zones" name="zona_persona" :error="form.errorsFor('zona_persona')" label="Zona"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.tiempo_ocupacion_habitual_persona" label="Tiempo de ocupacion habitual al momento del accidente" type="text" name="tiempo_ocupacion_habitual_persona" :error="form.errorsFor('tiempo_ocupacion_habitual_persona')" placeholder="Ej 5:40" help-text="Coloque el numero de horas seguido del numero de minutos separandolos con dos puntos"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="employee.salario_persona" label="Salario" type="number" name="" :error="form.errorsFor('salario_persona')" placeholder="Salario"></vue-input>
          </b-form-row> <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="employee.jornada_trabajo_habitual_persona" class="col-md-12" v-model="form.jornada_trabajo_habitual_persona" :options="shifts" name="jornada_trabajo_habitual_persona" :error="form.errorsFor('jornada_trabajo_habitual_persona')" label="Jorada de trabajo habitual"></vue-radio>
          </b-form-row> 
      </div>
</template>


<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput,
    VueRadio
  },
  props:{
    viewOnly: { type: Boolean, default: false },
    isEdit: { type: Boolean, default: false },
    form: { type: Object, required: true },
    employeeDetail: {
      default() {
        return {
            identification: '',
            name: '',
            sex: '',
            date_of_birth: '',
            income_date: '',
            antiquity: '',
            age: '',
            position: '',
            business: '',
            regional: '',
            headquarter: '',
            process: '',
            area: '',
            eps: '',
            afp: '',
            arl: ''
        };
      },
    },
    employee: {
      default() {
        return {
          tipo_vinculacion_persona: '',
          direccion_persona: '',
          departamento_persona_id: '',
          ciudad_persona_id: '',
          zona_persona: '',
          tiempo_ocupacion_habitual_persona: '',
          salario_persona: '',
          jornada_trabajo_habitual_persona: ''
        }
      }
    }    
  },
  watch: {
    employee() {
      this.loading = false;
      this.$emit('input', this.employee);
    }
  },
  computed: {
    dateBirth() {
      return this.formatDate(this.employeeDetail.date_of_birth)
    },
    incomeDate() {
      return this.formatDate(this.employeeDetail.income_date)
    }
  },
  data() {
    return {
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
      disabledDates: {
        from: new Date()
      }
    }
  },
  methods: {
    formatDate(param)
    {
      let date = ''

      if (param)
        date = new Date(param).toLocaleDateString()

      return date
    }
  }
}
</script>
