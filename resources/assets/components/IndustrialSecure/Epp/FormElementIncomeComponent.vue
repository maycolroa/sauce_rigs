<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-card  bg-variant="transparent" border-variant="dark" title="Información de Ingreso" class="mb-3 box-shadow-none">
      <div class="col-md-12">
        <b-form-row>
          <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_id" class="col-md-6" v-model="form.location_id" :error="form.errorsFor('location_id')"  name="location_id" label="Ubicación" placeholder="Seleccione la ubicación" :url="tagsLocationsDataUrl" :multiple="false" :selected-object="form.multiselect_location" :allowEmpty="true">
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
              <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="element.id_ele" :error="form.errorsFor(`elements_id.${index}.id_ele`)" name="id_ele" label="Elemento de protección personal" placeholder="Seleccione el elemento" :url="tagsElementsDataUrl" @change="typeElement(index)" :multiple="false" :allowEmpty="true" :selected-object="form.multiselect_element">
              </vue-ajax-advanced-select>
              <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="element.id_ele" name="id_ele" label="Elemento de protección personal" placeholder="Seleccione el elemento" :options="elements" :error="form.errorsFor(`elements_id.${index}.id_ele`)" @change="typeElement(index)" :allow-empty="false" :selected-object="element.multiselect_element">
                </vue-advanced-select>
              <vue-input v-if="element.type == 'No Identificable'" :disabled="viewOnly" class="col-md-6" v-model="element.quantity" label="Cantidad" type="number" name="quantity" :error="form.errorsFor(`elements_id.${index}.quantity`)" placeholder="Cantidad"></vue-input>
              <vue-advanced-select v-if="element.type == 'Identificable'" :disabled="viewOnly" class="col-md-12" v-model="element.codes" name="codes" label="Códigos de los elementos" placeholder="Agregue los códigos" :options="[]" :error="form.errorsFor(`elements_id.${index}.codes`)" :multiple="true" :allow-empty="false" :taggable="true">
                </vue-advanced-select>
            </b-form-row>
        </div>
      </template>

      <b-form-row style="padding-bottom: 20px;">
          <div class="col-md-12">
              <center><b-btn variant="primary" @click.prevent="addElement()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn></center>
          </div>
        </b-form-row>  
      
      </b-card>
    </b-card>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn @click="deletedTemporal" variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
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
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueInput,
    VueTextarea,
    VueAjaxAdvancedSelect,
    VueAdvancedSelect,
    VueFileSimple,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    employeesDataUrl: { type: String, default: "" },
    delivery: {
      default() {
        return {
          employee_id: '',
          position_employee: '',
          elements_id: [],
          location_id: '',
          files: [],
          firm_employee: '',
          observations: '',
          delete: {
            files: [],
            elements: []
          },
          edit_firm: 'NO',
          type: 'Entrega'
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
      this.elements = this.form.elementos
      this.form.elements_id.splice(0);

      this.form.elements_codes.forEach((eleme, key) => {
        this.form.elements_id.push({
          id: eleme.element.id,
          id_ele: eleme.element.id_ele,
          quantity: eleme.element.quantity,
          code: eleme.element.code,
          type: eleme.element.type,
          entry: eleme.element.entry
        })
        this.codes[key] = eleme.options
      })

      this.loading = false;
    }, 3000)
  },
  watch: {
    delivery() {
      this.loading = false;
      this.form = Form.makeFrom(this.delivery, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.delivery, this.method),
      tagsLocationsDataUrl: '/selects/eppLocations',
      tagsElementsDataUrl: '/selects/eppElements',
      elements: [],
      elements_position: [],
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
          this.$router.push({ name: "industrialsecure-epps-transactions-income" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    typeElement(index)
    {
      this.isLoading = true;
      axios.post('/industrialSecurity/epp/transaction/elementInfo', {id: this.form.elements_id[index].id_ele})
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
          code: []
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
