<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-card  bg-variant="transparent" border-variant="dark" title="Información de Salida" class="mb-3 box-shadow-none">
      <div class="col-md-12">
        <b-form-row>
          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.location_id" :error="form.errorsFor('location_id')"  name="location_id" label="Ubicación" placeholder="Seleccione la ubicación" :url="tagsLocationsDataUrl" :multiple="false" :selected-object="form.multiselect_location" :allowEmpty="true">
            </vue-ajax-advanced-select>
        </b-form-row>
      </div>
      <b-card  bg-variant="transparent" border-variant="dark" title="Elementos" class="mb-3 box-shadow-none">
      <template v-for="(element, index) in form.elements_id">
          <div :key="element.id">
            <b-form-row>
              <div class="col-md-12">
                  <div class="float-right">
                      <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeElement(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
              </div>
              <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="element.id_ele" name="id_ele" label="Elemento de protección personal" placeholder="Seleccione el elemento" :options="elements" :error="form.errorsFor(`elements_id.${index}.id_ele`)" @change="typeElement(index)" :allow-empty="false" :selected-object="element.multiselect_element" :searchable="true">
                </vue-advanced-select>
              <vue-input v-if="element.type == 'No Identificable'" :disabled="viewOnly" class="col-md-6" v-model="element.quantity" label="Cantidad" type="number" name="quantity" :error="form.errorsFor(`elements_id.${index}.quantity`)" placeholder="Cantidad"></vue-input>

              <vue-advanced-select v-if="element.type == 'Identificable'" :disabled="viewOnly" class="col-md-12" v-model="element.codes" name="codes" label="Código de elemento" placeholder="Seleccione el código" :options="codes[index]" :error="form.errorsFor(`elements_id.${index}.codes`)" :allow-empty="false" :multiple="true" :searchable="true">
                </vue-advanced-select>

              <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="element.reason" name="reason" :error="form.errorsFor(`elements_id.${index}.reason`)"  label="Motivo" placeholder="Seleccione el motivo" :url="tagsSReasonDataUrl" :multiple="false" :allowEmpty="true" :taggable="true"></vue-ajax-advanced-select-tag-unic>
            </b-form-row>
        </div>
      </template>

      <b-form-row style="padding-bottom: 20px;" v-if="!viewOnly">
          <div class="col-md-12">
              <center><b-btn variant="primary" v-if="form.location_id" @click.prevent="addElement()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn></center>
          </div>
        </b-form-row>  
      
      </b-card>
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

export default {
  components: {
    VueInput,
    VueTextarea,
    VueAjaxAdvancedSelect,
    VueAdvancedSelect,
    VueFileSimple,
    VueRadio,
    VueAjaxAdvancedSelectTagUnic
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    employeesDataUrl: { type: String, default: "" },
    exit: {
      default() {
        return {
          elements_id: [],
          location_id: '',
          delete: {
            elements: []
          }
        };
      }
    }
  },
  mounted() {
    if (this.isEdit || this.viewOnly)
    {
      this.cargar = false
    }
    
    setTimeout(() => {
      if (this.isEdit || this.viewOnly)
      {
        this.elements = this.form.elementos
        this.form.elements_id.splice(0);

        this.form.elements_codes.forEach((eleme, key) => {
          this.form.elements_id.push({
            id: eleme.element.id,
            id_ele: eleme.element.id_ele,
            quantity: eleme.element.quantity,
            codes: eleme.element.codes,
            type: eleme.element.type,
            reason: eleme.element.reason
          })
          this.codes[key] = eleme.options
        })
      }

      this.loading = false;
    }, 3000)
  },
  watch: {
    exit() {
      this.loading = false;
      this.form = Form.makeFrom(this.exit, this.method);
    },
    'form.location_id' () {
        this.uploadElements()
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.exit, this.method),
      tagsLocationsDataUrl: '/selects/eppLocations',
      //tagsElementsDataUrl: '/selects/eppElements',
      tagsSReasonDataUrl: '/selects/tagsReason',
      elements: [],
      codes: [],
      postData: {},
      noSi: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
      cargar: true
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-epps-transactions-exit" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    uploadElements()
    {
      this.isLoading = true;

      this.postData = Object.assign({}, {location_id: this.form.location_id});

      axios.post('/industrialSecurity/epp/exit/eppElementsLocations', this.postData)
      .then(response => {
          this.elements = response.data.multiselect

          response.data.codes.forEach((code, key) => {
            this.codes[key] = code
          })

          this.isLoading = false;
      })
      .catch(error => {
        console.log(error)
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    typeElement(index)
    {
      this.isLoading = true;
      axios.post('/industrialSecurity/epp/exit/elementInfo', {id: this.form.elements_id[index].id_ele})
        .then(response => {
            this.form.elements_id[index].type = response.data.type
        })
        .catch(error => {
            this.isLoading = false;
            Alerts.error('Error', 'Hubo un problema recolectando la información');
        });
    },
    addElement() 
    {
      this.form.elements_id.push({
          key: new Date().getTime(),
          id_ele: '',
          quantity: '',
          type: '',
          codes: []
      })
    },
    removeElement(index)
    {
      if (this.form.elements_id[index].id != undefined)
      {
        this.form.delete.elements.push(this.form.elements_id[index].id)
      }
      this.form.elements_id.splice(index, 1)
    },
  }
};
</script>
