<template>
      <div>
          <b-form-row>
            <vue-date-timepicker @input="getDayWeek()" :disabled="viewOnly" class="col-md-2" v-model="infor.fecha_accidente" label="Fecha del accidente" :full-month-name="true" placeholder="Seleccione la fecha" :error="form.errorsFor('fecha_accidente')" name="fecha_accidente" :disabled-dates="disabledDates" format="YYYY-MM-DD H:i"></vue-date-timepicker>
            <vue-input :disabled="true" class="col-md-5 offset-md-1" v-model="infor.dia_accidente" label="Dia de la semana en que ocurrio el accidente" type="text" name="dia_accidente" :error="form.errorsFor('dia_accidente')"></vue-input>
            <vue-radio :disabled="viewOnly" :checked="infor.jornada_accidente" class="col-md-3 offset-md-1" v-model="infor.jornada_accidente" :options="workingDayType" name="jornada_accidente" :error="form.errorsFor('jornada_accidente')" label="Jornada en que sucede"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="infor.estaba_realizando_labor_habitual" class="col-md-6" v-model="infor.estaba_realizando_labor_habitual" :options="siNo" name="estaba_realizando_labor_habitual" :error="form.errorsFor('estaba_realizando_labor_habitual')" label="¿Estaba realizando su labor habitual?"></vue-radio>
            <vue-input v-if="infor.estaba_realizando_labor_habitual == 'NO'" :disabled="viewOnly" class="col-md-6" v-model="infor.otra_labor_habitual" label="¿Cuál?" type="text" name="otra_labor_habitual" :error="form.errorsFor('otra_labor_habitual')" placeholder="¿Cuál?"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="infor.total_tiempo_laborado" label="Total tiempo laborado previo al accidente" type="text" name="total_tiempo_laborado" :error="form.errorsFor('total_tiempo_laborado')" placeholder="Ej 5:40" help-text="Coloque el numero de horas seguido del numero de minutos separandolos con dos puntos"></vue-input>
            <vue-radio :disabled="viewOnly" :checked="infor.accidente_ocurrio_dentro_empresa" class="col-md-6" v-model="infor.accidente_ocurrio_dentro_empresa" :options="companyAccident" name="accidente_ocurrio_dentro_empresa" :error="form.errorsFor('accidente_ocurrio_dentro_empresa')" label="Lugar donde ocurrió el accidente"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="infor.tipo_accidente" class="col-md-12" v-model="infor.tipo_accidente" :options="accidentTypes" name="tipo_accidente" :error="form.errorsFor('tipo_accidente')" label="Tipo de accidente"/>
          </b-form-row>
           <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-4" v-model="infor.departamento_accidente" :selected-object="infor.multiselect_departament_accident" name="departamento_accidente" :error="form.errorsFor('departamento_accidente')" label="Departamento" placeholder="Seleccione el departamento" :url="departamentsUrl"></vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-4" v-model="infor.ciudad_accidente" :selected-object="infor.multiselect_municipality_accident" name="ciudad_accidente" :error="form.errorsFor('ciudad_accidente')" label="Ciudad" placeholder="Seleccione la ciudad" :url="minicipalitiessUrl" :parameters="{departament: infor.departamento_persona_id }"></vue-ajax-advanced-select>
            <vue-radio :disabled="viewOnly" :checked="infor.zona_accidente" class="col-md-4" v-model="infor.zona_accidente" :options="zones" name="zona_accidente" :error="form.errorsFor('zona_accidente')" label="Zona"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="infor.causo_muerte" class="col-md-6" v-model="infor.causo_muerte" :options="siNo" name="causo_muerte" :error="form.errorsFor('causo_muerte')" label="¿Causó la muerte del trabajador?"></vue-radio>
            <vue-datepicker v-if="infor.causo_muerte == 'SI'" :disabled="viewOnly" class="col-md-4" v-model="infor.fecha_muerte" label="Fecha de la muerte" :full-month-name="true" placeholder="Seleccione la fecha de muerte" :error="form.errorsFor('fecha_muerte')" name="fecha_muerte" :disabled-dates="disabledDates">
                </vue-datepicker>
          </b-form-row>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="infor.agent_id" class="col-md-4" v-model="infor.agent_id" :options="agents" name="agent_id" :error="form.errorsFor('agent_id')" label="Agente del accidente (con que se lesionó el trabajador)" :stacked="true" custom-class="text-left"></vue-radio>
            <vue-radio :disabled="viewOnly" :checked="infor.mechanism_id" class="col-md-4" v-model="infor.mechanism_id" :options="sites" name="mechanism_id" :error="form.errorsFor('mechanism_id')" label="Mecanismo o forma del accidente" :stacked="true" custom-class="text-left"></vue-radio>
            <vue-radio :disabled="viewOnly" :checked="infor.site_id" class="col-md-4" v-model="infor.site_id" :options="mechanisms" name="site_id" :error="form.errorsFor('site_id')" label="Indique el sitio donde ocurrió el accidente" :stacked="true" custom-class="text-left"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-input v-if="infor.mechanism_id == 9" :disabled="viewOnly" class="col-md-6" v-model="infor.otro_mecanismo" label="Otro Mecanismo" type="text" name="otro_mecanismo" :error="form.errorsFor('otro_mecanismo')" placeholder="Otro Mecanismo"></vue-input>
            <vue-input v-if="infor.site_id == 9" :disabled="viewOnly" class="col-md-6" v-model="infor.otro_sitio" label="Otro sitio" type="text" name="otro_sitio" :error="form.errorsFor('otro_sitio')" placeholder="Otro sitio"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-checkbox style="padding-top: 20px;" :disabled="viewOnly" class="col-md-6" v-model="infor.parts_body" :checked="form.parts_body" label="Partes del cuerpo aparentemente afectado" name="parts_body" :options="partsBody" :vertical="true"></vue-checkbox>
            <vue-checkbox style="padding-top: 20px;" :disabled="viewOnly" class="col-md-6" v-model="infor.lesions_id" :checked="form.lesions_id" label="Tipo de lesión" name="lesions_id" :options="lesionTypes" :vertical="true"></vue-checkbox>
          </b-form-row>
          <b-form-row>
            <vue-input v-if="showOtherLesion" :disabled="viewOnly" class="col-md-6" v-model="infor.otra_lesion" label="Otra lesión" type="text" name="otra_lesion" :error="form.errorsFor('otra_lesion')" placeholder="Otro Mecanismo"></vue-input>
          </b-form-row>
      </div>
</template>


<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueDateTimepicker from "@/components/Inputs/VueDateTimepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
    VueCheckbox,
    VueTextarea,
    VueDateTimepicker
  },
  props:{
    viewOnly: { type: Boolean, default: false },
    form: { type: Object, required: true },
    isEdit: { type: Boolean, default: false },
    infor: {
      default() {
        return {
          fecha_accidente: '',
          dia_accidente: '',
          jornada_accidente: '',
          estaba_realizando_labor_habitual: '',
          otra_labor_habitual: '',
          total_tiempo_laborado: '',
          tipo_accidente: '',
          departamento_accidente: '',
          ciudad_accidente: '',
          zona_accidente: '',
          accidente_ocurrio_dentro_empresa: '',
          causo_muerte: '',
          fecha_muerte: '',
          otro_sitio: '',
          otro_mecanismo: '',
          otra_lesion: '',
          site_id: '',
          agent_id: '',
          mechanism_id: '',
          lesions_id: [],
          parts_body: [],
        }
      }
    },
  },
  data() {
    return {
      departamentsUrl: '/selects/departaments',
      minicipalitiessUrl: '/selects/municipalities',  
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
      workingDayType: [
        {text: 'Normal', value: 'Normal'},
        {text: 'Extra', value: 'Extra'}
      ],
      zones: [
         {text: 'Urbana', value: 'Urbana'},
         {text: 'Rural', value: 'Rural'}
      ],
      companyAccident: [
        {text: 'Dentro de la empresa', value: 'Dentro de la empresa'},
        {text: 'Fuera de la empresa', value: 'Fuera de la empresa'}
      ],
      accidentTypes: [
        {text: 'Violencia', value: 'Violencia'},
        {text: 'Tránsito', value: 'Tránsito'},
        {text: 'Deportivo', value: 'Deportivo'},
        {text: 'Recreativo o cultural', value: 'Recreativo o cultural'},
        {text: 'Propios del trabajo', value: 'Propios del trabajo'}
      ],
      disabledDates: {
        from: new Date()
      },
      agents: [],
      sites: [],
      mechanisms: [],
      lesionTypes: [],
      partsBody: [],
      showOtherLesion: false
    }
  },
  watch: {
    infor() {
      this.loading = false;
      this.$emit('input', this.infor);
    },
    'infor.lesions_id' () {
      if (this.infor.lesions_id.includes(16))
        this.showOtherLesion = true
      else
         this.showOtherLesion = false
    }
  },
  created(){
    this.fetchSelect('agents', '/radios/agents')
    this.fetchSelect('sites', '/radios/sites')
    this.fetchSelect('mechanisms', '/radios/mechanisms')
    this.fetchSelect('lesionTypes', '/radios/lesionTypes')
    this.fetchSelect('partsBody', '/radios/partsBody')

    this.getDayWeek()
  },
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
            //Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            //this.$router.go(-1);
        });
    },
    getDayWeek()
    {
      var date = new Date(this.infor.fecha_accidente);
      var day = date.getDay();

      if (day == 1)
      {
        this.infor.dia_accidente = 'Lunes'
      }
      if (day == 2)
      {
        this.infor.dia_accidente = 'Martes'
      }
      if (day == 3)
      {
        this.infor.dia_accidente = 'Miercoles'
      }
      if (day == 4)
      {
        this.infor.dia_accidente = 'Jueves'
      }
      if (day == 5)
      {
        this.infor.dia_accidente = 'Viernes'
      }
      if (day == 6)
      {
        this.infor.dia_accidente = 'Sabado'
      }
      if (day == 7)
      {
        this.infor.dia_accidente = 'Domingo'
      }
    }
  }
}
</script>