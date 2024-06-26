<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.code" label="Código" type="text" name="code" :error="form.errorsFor('code')" placeholder="Código"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-radio :disabled="viewOnly" class="col-md-6" v-model="form.class_element" :options="classElement" name="class_element" :error="form.errorsFor('class_element')" label="Clase" :checked="form.class_element"></vue-radio>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.type" name="type" :error="form.errorsFor('type')" label="Tipo de elemento" placeholder="Seleccione el Tipo de elemento" :url="tagsTypeDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="form.mark" name="mark" :error="form.errorsFor('mark')" label="Marca" placeholder="Seleccione la marca" :url="tagsMarkDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
      </vue-ajax-advanced-select-tag-unic>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.size" label="Talla" type="text" name="size" :error="form.errorsFor('size')" placeholder="Talla"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.cost" label="Costo" type="number" name="cost" :error="form.errorsFor('cost')" placeholder="Costo"></vue-input>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.description" label="Descripción" name="description" ::error="form.errorsFor('description')"  placeholder="Descripción"></vue-textarea>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.applicable_standard" name="applicable_standard" :error="form.errorsFor('applicable_standard')" label="Normas Aplicables" placeholder="Seleccione la norma" :url="tagsStandarApplyDataUrl" :multiple="true" :allowEmpty="true" :taggable="true"></vue-ajax-advanced-select>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.observations" label="Observaciones" name="observations" ::error="form.errorsFor('observations')"  placeholder="Observaciones"></vue-textarea>
    </b-form-row>

    <b-form-row>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.operating_instructions" label="Instrucciones de uso" name="operating_instructions" ::error="form.errorsFor('operating_instructions')"  placeholder="Instrucciones de uso"></vue-textarea>
      <vue-radio :disabled="viewOnly" class="col-md-6" v-model="form.state" :options="actInac" name="state" :error="form.errorsFor('state')" label="Estado" :checked="form.state"></vue-radio>
      <!--<vue-radio v-if="auth.inventaryEpp == 'SI'" :disabled="viewOnly" class="col-md-4" v-model="form.identify_each_element" :options="siNo" name="identify_each_element" :error="form.errorsFor('identify_each_element')" label="¿Desea identificar cada elemento?" :checked="form.identify_each_element"></vue-radio>-->
      </b-form-row>

    <b-form-row>
      <vue-radio :disabled="viewOnly" class="col-md-6" v-model="form.reusable" :options="siNo" name="reusable" :error="form.errorsFor('reusable')" label="Reutilizable" :checked="form.reusable"></vue-radio>
      <vue-radio :disabled="viewOnly" class="col-md-6" v-model="form.expiration_date" :options="siNo" name="expiration_date" :error="form.errorsFor('expiration_date')" label="¿Tiene máximo tiempo de uso?" :checked="form.expiration_date"></vue-radio>
    </b-form-row>

    <b-form-row>
      <vue-input v-if="form.expiration_date == 'SI'" :disabled="viewOnly" class="col-md-6" v-model="form.days_expired" label="Máximos dias de uso" type="number" name="days_expired" :error="form.errorsFor('days_expired')" placeholder="Máximos dias de uso"></vue-input>
    </b-form-row>

    <b-form-row v-if="form.class_element == 'Equipo'">
      <vue-file-simple :disabled="viewOnly" :help-text="(form.id && form.data_sheet) ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/epp/element/downloadDataSheet/${form.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="form.data_sheet" label="Ficha Técnica" name="data_sheet" placeholder="Seleccione un archivo" :error="form.errorsFor(`data_sheet`)" :maxFileSize="20"/>
      <vue-file-simple :disabled="viewOnly" :help-text="(form.id && form.user_manual) ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/epp/element/downloadUserManual/${form.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="form.user_manual" label="Manual de uso" name="user_manual" placeholder="Seleccione un archivo" :error="form.errorsFor(`user_manual`)" :maxFileSize="20"/>
    </b-form-row>

    <b-form-row>
      <template v-if="form.id && form.image && form.path">
          <center>
              <div class="my-4 mx-2 text-center">
                  <img class="mw-100" :src="`${form.path}`" alt="Max-width 100%">
              </div>
          </center>
      </template>
      <vue-file-simple :disabled="viewOnly" :help-text="(form.id && form.image) ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/epp/element/download/${form.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="form.image" label="Imagen" name="image" placeholder="Seleccione un archivo" :error="form.errorsFor(`image`)" :maxFileSize="20"/>
    </b-form-row>

    <b-form-row>
      <vue-radio :disabled="viewOnly" class="col-md-12" v-model="form.stock_minimun" :options="siNo" name="stock_minimun" :error="form.errorsFor('stock_minimun')" label="¿Desea configurar stock mínimo para este elemento?" :checked="form.stock_minimun"></vue-radio>
    </b-form-row>

    <b-card v-if="form.stock_minimun == 'SI'" bg-variant="transparent" border-variant="dark" title="Ubicaciones" class="mb-3 box-shadow-none">
        <b-card>
          <template v-for="(location, index) in form.locations_stock">
            <div :key="location.id">
              <b-form-row>
                <div class="col-md-12">
                    <div class="float-right">
                        <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeLocation(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                    </div>
                </div>
                <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="location.id_loc" :error="form.errorsFor(`locations_stock.${index}.id_loc`)" name="id_loc" label="Ubicación" placeholder="Seleccione la ubicación" :url="tagsLocationsDataUrl" :multiple="false" :selected-object="location.multiselect_location" :emptyAll="true" >
              </vue-ajax-advanced-select>
                <vue-input :disabled="viewOnly" class="col-md-6" v-model="location.quantity" label="Cantidad" type="number" name="quantity" :error="form.errorsFor(`locations_stock.${index}.quantity`)" placeholder="Cantidad"></vue-input>
              </b-form-row>
            </div>
          </template>
        </b-card>
    </b-card>
    <b-form-row style="padding-bottom: 20px;">
      <div class="col-md-12">
          <center><b-btn v-if="form.stock_minimun == 'SI'" variant="primary" @click.prevent="addLocation()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn></center>
      </div>
    </b-form-row>  

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
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueTextarea,
    VueAjaxAdvancedSelect,
    VueRadio,
    VueFileSimple,
    VueAjaxAdvancedSelectTagUnic
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    element: {
      default() {
        return {
            name: '',
            code: '',
            type: [],
            mark: [],
            size: '',
            description: '',
            class_element: '',
            observations: '',
            state: '',
            reusable: '',
            image: '',
            operating_instructions: '',
            applicable_standard: [],
            identify_each_element: '',
            expiration_date: '',
            locations_stock: [],
            data_sheet: '',
            user_manual: '',
            cost: ''
        };
      }
    }
  },
  watch: {
    element() {
      this.loading = false;
      this.form = Form.makeFrom(this.element, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.element, this.method),
      tagsTypeDataUrl: '/selects/tagsTypeEpp',
      tagsMarkDataUrl: '/selects/tagsMarkEpp',
      tagsStandarApplyDataUrl: '/selects/tagsStandarApplyEpp',
      actInac: [
          {text: 'Activo', value: 'Activo'},
          {text: 'Inactivo', value: 'Inactivo'}
      ],
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
      classElement: [
        {text: 'Elemento de protección personal', value: 'Elemento de protección personal'},
        {text: 'Dotación', value: 'Dotación'},
        {text: 'Equipo', value: 'Equipo'}
      ],
      inventary: auth.inventaryEpp,
      tagsLocationsDataUrl: '/selects/eppLocations',
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form.inventary = this.inventary;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-epps-elements" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addLocation() 
    {
      this.form.locations_stock.push({
          key: new Date().getTime(),
          id_loc: '',
          quantity: '',
          
      })
    },
    removeLocation(index)
    {
      if (this.form.locations_stock[index].id != undefined)
      {
        this.form.delete.elements.push(this.form.locations_stock[index].id)
      }
      this.form.locations_stock.splice(index, 1)
    },
  }
};
</script>
