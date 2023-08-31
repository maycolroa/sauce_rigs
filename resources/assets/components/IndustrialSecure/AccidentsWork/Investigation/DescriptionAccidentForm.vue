<template>
      <div>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="description.investigation_arl" class="col-md-6 offset-md-3" v-model="description.investigation_arl" :options="siNo" name="investigation_arl" :error="form.errorsFor('investigation_arl')" label="¿Se debe enviar investigacion a la ARL?"></vue-radio>
            <vue-datepicker v-if="description.investigation_arl == 'SI'" :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="description.fecha_envio_arl" label="Fecha en que se envía la investigación a la ARL" :full-month-name="true" placeholder="Seleccione la fecha" :error="form.errorsFor('fecha_envio_arl')" name="fecha_envio_arl" :disabled-dates="disabledDates">
                </vue-datepicker>
          </b-form-row>
          <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="description.fecha_diligenciamiento_informe" label="Fecha de la investigacion" :full-month-name="true" placeholder="Seleccione la fecha" :error="form.errorsFor('fecha_diligenciamiento_informe')" name="fecha_diligenciamiento_informe" :disabled-dates="disabledDates"></vue-datepicker>        
          </b-form-row>
          <b-form-row>
            <label class="col-md-6 offset-md-4">Responsable de la investigación</label>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="description.nombres_apellidos_responsable_informe" label="Nombre" type="text" name="nombres_apellidos_responsable_informe" :error="form.errorsFor('nombres_apellidos_responsable_informe')" placeholder="Nombre"></vue-input>
            <vue-radio :disabled="viewOnly" :checked="description.tipo_identificacion_responsable_informe" class="col-md-6" v-model="description.tipo_identificacion_responsable_informe" :options="typesDocuments" name="tipo_identificacion_responsable_informe" :error="form.errorsFor('tipo_identificacion_responsable_informe')" label="Tipo de identificación"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="description.identificacion_responsable_informe" label="Número de identificación" type="text" name="identificacion_responsable_informe" :error="form.errorsFor('identificacion_responsable_informe')" placeholder="Documento"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="description.cargo_responsable_informe" label="Cargo" type="text" name="cargo_responsable_informe" :error="form.errorsFor('cargo_responsable_informe')" placeholder="Cargo"></vue-input>
          </b-form-row>
          <center>
            <div v-if="form.old_firm" class="my-4 mx-2 text-center">
              <label class="col-md-6">Firma del responsable del informe</label>
              <br>
                <img class="ui-w-200" :src="`${form.firm_image}`" alt="">
            </div>
          </center>
          <b-form-row>
            <vue-textarea class="col-md-12" :disabled="viewOnly" v-model="description.descripcion_accidente" label="Descripción del accidente. Responda a las preguntas qué pasó, cuándo, dónde, cómo y por qué" name="descripcion_accidente" placeholder="Descripción" rows="4"></vue-textarea> 
          </b-form-row>  
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="description.personas_presenciaron_accidente" class="col-md-6 offset-md-3" v-model="description.personas_presenciaron_accidente" :options="siNo" name="personas_presenciaron_accidente" :error="form.errorsFor('personas_presenciaron_accidente')" label="¿Hubo personas que presenciaron el accidente?"></vue-radio>
          </b-form-row>
          <template v-if="description.personas_presenciaron_accidente == 'SI'">
            <person-add
            :persons="form.persons"
            :view-only="viewOnly"
            :is-edit="isEdit"
            :form="form"
            rol='Presencio Accidente'/>
          </template>
      </div>
</template>


<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import PersonAdd from '@/components/IndustrialSecure/AccidentsWork/PersonAddComponent.vue';

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
    PersonAdd,
    VueTextarea

  },
  props:{
    viewOnly: { type: Boolean, default: false },
    form: { type: Object, required: true },
    isEdit: { type: Boolean, default: false },
    description: {
      default() {
        return {
          investigation_arl: '',
          fecha_envio_arl: '',
          nombres_apellidos_responsable_informe: '',
          cargo_responsable_informe: '',
          tipo_identificacion_responsable_informe: '',
          identificacion_responsable_informe: '',
          fecha_diligenciamiento_informe: '',
          descripcion_accidente: '',
          personas_presenciaron_accidente: '',
          persons: {
             persons: [],
             delete: []
          },
          firm_image: '',
          old_firm: ''
        }
      }
    },
  },

  watch: {
    description() {
      this.loading = false;
      this.$emit('input', this.description);
    }
  },
  data() {
    return {  
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
      disabledDates: {
        from: new Date()
      }
    }
  }
}
</script>
