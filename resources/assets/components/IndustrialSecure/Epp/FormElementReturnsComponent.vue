<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-card  bg-variant="transparent" border-variant="dark" title="Información Empleado" class="mb-3 box-shadow-none">
      <div class="col-md-12">
        <b-form-row>
          <vue-ajax-advanced-select class="col-md-6" :disabled="viewOnly" v-model="form.employee_id"  name="employee_id" :label="keywordCheck('employee')" placeholder="Seleccione una opción" :url="employeesDataUrl" :selected-object="form.multiselect_employee" :error="form.errorsFor('employee_id')"> </vue-ajax-advanced-select>
          <vue-input :disabled="true" class="col-md-6" v-model="form.position_employee" label="Cargo" type="text" name="position_employee" :error="form.errorsFor('position_employee')" placeholder="Cargo"></vue-input>
          <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_id" class="col-md-6" v-model="form.location_id" :error="form.errorsFor('location_id')"  name="location_id" label="Ubicación" placeholder="Seleccione la ubicación" :url="tagsLocationsDataUrl" :multiple="false" :selected-object="form.multiselect_location" :allowEmpty="true">
            </vue-ajax-advanced-select>
        </b-form-row>
      </div>
      <b-card  bg-variant="transparent" border-variant="dark" title="Elementos" class="mb-3 box-shadow-none">
      <b-form-row style="padding-bottom: 20px;">
        <div class="col-md-12">
            <center><b-btn v-if="form.employee_id" variant="primary" @click="showModalHistory()" ><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Ver historial de desechos</b-btn></center>
        </div>
        <show-history-qualify
          v-if="idHistory"
          :id="idHistory"
          @close-modal-history="closeModalHistory"
        />
      </b-form-row>
      <template v-for="(element, index) in form.elements_id">
          <div :key="element.key">
            <b-form-row>
              <div class="col-md-12">
                  <div class="float-right">
                      <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeElement(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
              </div>
              <vue-advanced-select :disabled="true" class="col-md-6" v-model="element.id_ele" name="id_ele" label="Elemento de protección personal" placeholder="Seleccione el elemento" :options="elements" :error="form.errorsFor(`elements_id.${index}.id_ele`)" @change="typeElement(index)" :allow-empty="false" :selected-object="element.multiselect_element">
                </vue-advanced-select>
              <vue-input v-if="element.type == 'No Identificable'" :disabled="true" class="col-md-6" v-model="element.quantity" label="Cantidad" type="number" name="quantity" :error="form.errorsFor(`elements_id.${index}.quantity`)" placeholder="Cantidad"></vue-input>
              <vue-input v-if="element.type == 'Identificable'" :disabled="true" class="col-md-12" v-model="element.code" label="Código" type="text" name="code" :error="form.errorsFor(`elements_id.${index}.code`)" placeholder="Código"></vue-input>
              <vue-radio :disabled="viewOnly" class="col-md-6" v-model="element.waste" :options="noSi" :name="`waste_${index}`" :error="form.errorsFor(`elements_id.${index}.waste`)" label="¿Se desechara el elemento?" :checked="element.waste"></vue-radio>
              <vue-radio :disabled="viewOnly" class="col-md-6" v-model="element.rechange" :options="noSi" :name="`rechange_${index}`" :error="form.errorsFor(`elements_id.${index}.rechange`)" label="¿Se hara un cambio de unidad del elemento?" :checked="element.rechange"></vue-radio>

              <vue-input v-if="element.type == 'No Identificable' && element.rechange == 'SI'" :disabled="viewOnly" class="col-md-12" v-model="element.quantity_rechange" label="Cantidad a cambiar" type="text" name="quantity_rechange" :error="form.errorsFor(`elements_id.${index}.quantity_rechange`)" placeholder="Cantidad a cambiar" ></vue-input>

              <vue-advanced-select v-if="element.type == 'Identificable' && element.rechange == 'SI'" :disabled="viewOnly" class="col-md-12" v-model="element.code" name="code" label="Código de elemento" placeholder="Seleccione el código" :options="codes[index]" :error="form.errorsFor(`elements_id.${index}.code`)" @selectedName="hashSelected(index)" :allow-empty="false">
                </vue-advanced-select>

              <vue-ajax-advanced-select-tag-unic v-if="element.rechange == 'SI'" :disabled="viewOnly" class="col-md-12" v-model="element.reason" name="reason" :error="form.errorsFor(`elements_id.${index}.reason`)" label="Motivo" placeholder="Seleccione el motivo" :url="tagsSReasonDataUrl" :multiple="true" :allowEmpty="true" :taggable="true"></vue-ajax-advanced-select-tag-unic>
            </b-form-row>
        </div>
      </template>      
      </b-card>
    </b-card>

    <b-card bg-variant="transparent" border-variant="dark" title="Evidencias" class="mb-3 box-shadow-none">
      <template v-for="(file, index) in form.files">
          <div :key="file.key">
            <b-form-row>
              <template v-if="file.path">
                  <center>
                      <div class="my-4 mx-2 text-center">
                          <img class="mw-100" :src="`${file.path}`" alt="Max-width 100%">
                      </div>
                  </center>
              </template>
              <div class="col-md-12">
                  <div class="float-right">
                      <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeFile(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
              </div>
              <vue-file-simple :disabled="viewOnly" :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/epp/transaction/download/file/${file.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`files.${index}.file`)" :maxFileSize="20"/>
            </b-form-row>
        </div>
      </template>

      <b-form-row style="padding-bottom: 20px;">
          <div class="col-md-12">
              <center><b-btn variant="primary" @click.prevent="addFile()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn></center>
          </div>
        </b-form-row>  
    </b-card>

    <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
      <div>
        <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.observations" label="Observaciones (Opcional)" name="observations" placeholder="Observaciones" :error="form.errorsFor('observations')" rows="3"></vue-textarea>              
      </div>
      <div class="row">
        <div class="col-md-12">
          <center>
              <div class="my-4 mx-2 text-center" v-if="form.old_firm">
                  <img class="ui-w-300" :src="`${form.firm_image}`" alt="">
              </div>
          </center>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <center>              
            <vue-radio :disabled="viewOnly" class="col-md-6" v-model="form.edit_firm" :options="noSi" name="edit_firm" :error="form.errorsFor('edit_firm')" label="¿Desea agregar una firma?" :checked="form.edit_firm"></vue-radio>      
          </center>
        </div>
      </div>
      <div v-if="form.edit_firm == 'SI'">
          <center>
              <p><b>Ingresa aqui tu firma</b></p>
              <VueSignaturePad
                  id="signature"
                  width="100%"
                  height="250px"
                  ref="signaturePad"
                  v-model="form.firm_employee"
              />
              <br>
              <div>
                  <b-btn variant="default" @click="undo">Borrar</b-btn>
              </div>
              <br>
          </center>
      </div>
        
    </b-card>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" @click="deletedTemporal" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
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
import ShowHistoryQualify from "./ShowHistoryWastesComponent.vue";
import Alerts from '@/utils/Alerts.js';
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";

export default {
  components: {
    VueInput,
    VueTextarea,
    VueAjaxAdvancedSelect,
    VueAdvancedSelect,
    VueFileSimple,
    ShowHistoryQualify,
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
    returns: {
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
          type: 'Devolucion',
          inventary: auth.inventaryEpp,
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
            code: eleme.element.code,
            type: eleme.element.type,
            key: eleme.element.key,
            waste: eleme.element.wastes
          })
          this.codes[key] = eleme.options
        })
      }

      this.loading = false;
    }, 3000)
  },
  watch: {
    returns() {
      this.loading = false;
      this.form = Form.makeFrom(this.returns, this.method);
    },
    'form.employee_id' () {
      this.updateDetails(`/industrialSecurity/epp/transaction/employeeReturns/${this.form.employee_id}`)

      this.form.location_id = '';
    },
    'form.location_id' () {
        this.uploadElements()
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.returns, this.method),
      tagsLocationsDataUrl: '/selects/eppLocations',
      elements: [],
      codes: [],
      elements_position: [],
      postData: {},
      noSi: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
      cargar: true,
      idHistory: '', 
      tagsSReasonDataUrl: '/selects/tagsReason',
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

      this.form.clearFilesBinary();
        _.forIn(this.form.files, (file, keyFile) => {
          if (file.file)
            this.form.addFileBinary(`${keyFile}`, file.file);
        });

      if (this.form.edit_firm == 'SI')
      {
        const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
        if (data != null) {
          this.form.firm_employee = data
        }
      }

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-epps-transactions-returns" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    updateDetails(url)
    {
      this.isLoading = true;
      axios.get(url)
      .then(response => {
          this.form.position_employee = response.data.data.position_employee;
          this.isLoading = false;
      })
      .catch(error => {
        console.log(error)
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
     uploadElements()
    {
      this.isLoading = true;

      this.postData = Object.assign({}, {employee_id: this.form.employee_id}, {location_id: this.form.location_id});

      axios.post('/industrialSecurity/epp/transaction/returns/eppElementsLocations', this.postData)
      .then(response => {
          this.form.elements_id.splice(0);
          this.elements = response.data.data.multiselect

          response.data.data.elements.forEach((eleme, key) => {
            this.form.elements_id.push({
              id: eleme.element.id,
              id_ele: eleme.element.id_ele,
              quantity: eleme.element.quantity,
              code: eleme.element.code,
              type: eleme.element.type,
              key: eleme.element.key,
              waste: eleme.element.wastes
            });

            this.codes[key] = eleme.options
          });          
           
          this.isLoading = false;
      })
      .catch(error => {
        console.log(error)
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    undo () {
      this.$refs.signaturePad.undoSignature()
    },
    addFile() 
    {
      this.form.files.push({
          key: new Date().getTime(),
          file: ''
      })
    },
    removeFile(index)
    {
      if (this.form.files[index].id != undefined)
        this.form.delete.files.push(this.form.files[index].id)

      this.form.files.splice(index, 1)
    },
    removeElement(index)
    {
      if (this.form.elements_id[index].id != undefined)
      {
        this.form.delete.elements.push(this.form.elements_id[index].id)
      }
      this.form.elements_id.splice(index, 1)
    },
    showModalHistory() {
      this.idHistory = this.form.employee_id
    },
    closeModalHistory() {
      this.idHistory = ''
    },
    hashSelected(index)
    {
      this.isLoading = true;
      axios.post('/industrialSecurity/epp/transaction/hashSelected', {id: this.form.elements_id[index].id_ele, location_id: this.form.location_id, select_hash: this.form.elements_id[index].code})
        .then(response => {
            /*this.form.elements_id[index].type = response.data.type
            this.codes[index] = response.data.options*/
        })
        .catch(error => {
            this.isLoading = false;
            Alerts.error('Error', 'El código seleccionado no existe o no se encuentra disponible en la ubicación seleccionada');
        });
    },
    deletedTemporal()
    {
      this.isLoading = true;
      axios.post('/industrialSecurity/epp/transaction/deletedTemporal')
        .then(response => {
            /*this.form.elements_id[index].type = response.data.type
            this.codes[index] = response.data.options*/
        })
        .catch(error => {
            this.isLoading = false;
            Alerts.error('Error', 'Hubo un problema recolectando la información');
        });
    },
  }
};
</script>
<style>
  #signature {
    border: double 3px transparent;
    border-radius: 5px;
    background-image: linear-gradient(white, white),
      radial-gradient(circle at top left, #9f6274, #9f6274);
    background-origin: border-box;
    background-clip: content-box, border-box;
  }
</style>
