<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-card  bg-variant="transparent" border-variant="dark" title="Información de Ingreso" class="mb-3 box-shadow-none">
      <div class="col-md-12">
        <b-form-row>
          <vue-ajax-advanced-select :disabled="true" class="col-md-6" v-model="form.location_origin_id" :error="form.errorsFor('location_origin_id')"  name="location_origin_id" label="Ubicación Origen" placeholder="Seleccione la ubicación de origen" :url="tagsLocationsDataUrl" :multiple="false" :selected-object="form.multiselect_location_origin" :allowEmpty="true">
            </vue-ajax-advanced-select>
          <vue-ajax-advanced-select :disabled="true" class="col-md-6" v-model="form.location_destiny_id" :error="form.errorsFor('location_destiny_id')"  name="location_destiny_id" label="Ubicación Destino" placeholder="Seleccione la ubicación de destino" :url="tagsLocationsDataUrl" :multiple="false" :selected-object="form.multiselect_location_destiny" :allowEmpty="true">
            </vue-ajax-advanced-select>
        </b-form-row>
      </div>
      <b-card  bg-variant="transparent" border-variant="dark" title="Elementos" class="mb-3 box-shadow-none">
      <template v-for="(element, index) in form.elements_id">
          <div :key="element.id">
            <b-form-row>
              <vue-advanced-select :disabled="true" class="col-md-6" v-model="element.id_ele" name="id_ele" label="Elemento de protección personal" placeholder="Seleccione el elemento" :options="elements" :error="form.errorsFor(`elements_id.${index}.id_ele`)" @change="typeElement(index)" :allow-empty="false" :selected-object="element.multiselect_element" :searchable="true">
                </vue-advanced-select>

              <vue-input v-if="element.type == 'No Identificable'" :disabled="true" class="col-md-6" v-model="element.quantity_transfer" label="Cantidad Trasladada" type="number" name="quantity_transfer" :error="form.errorsFor(`elements_id.${index}.quantity_transfer`)" placeholder="Cantidad Trasladada"></vue-input>

              <vue-advanced-select v-if="element.type == 'Identificable'" :disabled="true" class="col-md-12" v-model="element.codes_transfer" name="codes_transfer" label="Códigos Transferidos" placeholder="Seleccione el código" :options="codes_transfer[element.id_ele]" :error="form.errorsFor(`elements_id.${index}.codes_transfer`)" :allow-empty="false" :multiple="true" :searchable="true">
                </vue-advanced-select>

              <vue-radio :disabled="viewOnly" class="col-md-6" v-model="element.reception" :options="siNo" :name="`reception${index}`" :error="form.errorsFor(`elements_id.${index}.reception`)" label="¿Se recibio el elemento?" :checked="element.reception"></vue-radio>

              <vue-radio v-if="element.reception == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="element.quantity_complete" :options="siNo" :name="`quantity_complete${index}`" :error="form.errorsFor(`elements_id.${index}.quantity_complete`)" label="¿Se recibio la cantidad completa?" :checked="element.quantity_complete"></vue-radio>

               <vue-input v-if="element.type == 'No Identificable' && element.reception == 'SI' && element.quantity_complete == 'NO'" :disabled="viewOnly" class="col-md-6" v-model="element.quantity_reception" label="Cantidad Recibida" type="number" name="quantity_reception" :error="form.errorsFor(`elements_id.${index}.quantity_reception`)" placeholder="Cantidad Recibida"></vue-input>              
                
              <vue-advanced-select v-if="element.type == 'Identificable' && element.reception == 'SI'" :disabled="viewOnly" class="col-md-12" v-model="element.codes_reception" name="codes_reception" label="Códigos Recibidos" placeholder="Seleccione el código" :options="codes_transfer[element.id_ele]" :error="form.errorsFor(`elements_id.${index}.codes_reception`)" :allow-empty="false" :multiple="true" :searchable="true">
                </vue-advanced-select>

              <vue-ajax-advanced-select-tag-unic v-if="element.reception == 'NO' || element.quantity_complete == 'NO'" :disabled="viewOnly" class="col-md-6" v-model="element.reason" name="reason" :error="form.errorsFor(`elements_id.${index}.reason`)" label="Motivo" placeholder="Seleccione el motivo" :url="tagsSReasonDataUrl" :multiple="false" :allowEmpty="true" :taggable="true"></vue-ajax-advanced-select-tag-unic>
            </b-form-row>
        </div>
      </template>      
      </b-card>

      <div class="col-md-12">
        <b-form-row>
          <vue-checkbox-simple style="padding-top: 30px;" :disabled="viewOnly" class="col-md-6" v-model="form.state" label="¿Cerrar Traslado?" :checked="form.state" name="state" checked-value="Recibido" unchecked-value="En espera"></vue-checkbox-simple>
        </b-form-row>
      </div>

    </b-card>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import Alerts from '@/utils/Alerts.js';
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";

export default {
  components: {
    VueInput,
    VueTextarea,
    VueAjaxAdvancedSelect,
    VueAdvancedSelect,
    VueFileSimple,
    VueRadio,
    VueAjaxAdvancedSelectTagUnic,
    VueCheckboxSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    reception: {
      default() {
        return {
          elements_id: [],
          location_origin_id: '',
          location_destiny_id: '',
          state: '',
          delete: {
            elements: []
          }
        };
      }
    }
  },
  mounted() {    
    setTimeout(() => {
      this.elements = this.form.elementos
      this.form.elements_id.splice(0);

      this.form.elements_codes.forEach((eleme, key) => {
        this.form.elements_id.push({
          id: eleme.element.id,
          id_ele: eleme.element.id_ele,
          quantity_transfer: eleme.element.quantity_transfer,
          quantity_reception: eleme.element.quantity_reception,
          reception: eleme.element.reception,
          codes_transfer: eleme.element.codes_transfer,
          codes_reception: eleme.element.codes_reception,
          type: eleme.element.type,
          multiselect_element: eleme.element.multiselect_element,
          quantity_complete: eleme.element.quantity_complete
        })
        this.codes_transfer[eleme.element.id_ele] = eleme.options
      })

      this.loading = false;
    }, 3000)
  },
  watch: {
    reception() {
      this.loading = false;
      this.form = Form.makeFrom(this.reception, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.reception, this.method),
      tagsLocationsDataUrl: '/selects/eppLocations',
      tagsElementsDataUrl: '/selects/eppElements',
      tagsSReasonDataUrl: '/selects/tagsReason',
      elements: [],
      codes_transfer: [],
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-epps-transactions-reception" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
