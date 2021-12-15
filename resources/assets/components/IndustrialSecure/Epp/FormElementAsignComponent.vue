<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-card  bg-variant="transparent" border-variant="dark" title="Información Empleado" class="mb-3 box-shadow-none">
      <div class="col-md-12">
        <b-form-row>
          <vue-ajax-advanced-select class="col-md-6" :disabled="viewOnly" v-model="form.employee_id"  name="employee_id" :label="keywordCheck('employee')" placeholder="Seleccione una opción" :url="employeesDataUrl" :selected-object="form.multiselect_employee" :error="form.errorsFor('employee_id')"> </vue-ajax-advanced-select>
          <vue-input :disabled="true" class="col-md-6" v-model="form.position_employee" label="Cargo" type="text" name="position_employee" :error="form.errorsFor('position_employee')" placeholder="Cargo"></vue-input>
          <vue-ajax-advanced-select :disabled="viewOnly || !form.employee_id" class="col-md-6" v-model="form.location_id" :error="form.errorsFor('location_id')"  name="location_id" label="Ubicación" placeholder="Seleccione la ubicación" :url="tagsLocationsDataUrl" :multiple="false" :allowEmpty="true">
            </vue-ajax-advanced-select>
        </b-form-row>
      </div>
      <b-card  bg-variant="transparent" border-variant="dark" title="Elementos" class="mb-3 box-shadow-none">
      <template v-for="(element, index) in form.elements_id">
          <div :key="element.id">
            <b-form-row>
              <div class="col-md-12">
                  <div class="float-right">
                      <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeElement(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
              </div>
              <!--<vue-advanced-select class="col-md-6" v-model="element.type" :multiple="false" :options="typesElement" :hide-selected="false" name="type" label="Tipo de elemento" placeholder="Seleccione el tipo de elemento"></vue-advanced-select>-->
              <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="element.id_ele" name="id_ele" label="Elemento de protección personal" placeholder="Seleccione el elemento" :options="elements" :error="form.errorsFor(`elements_id.${index}.id_ele`)" @change="typeElement(index)">
                </vue-advanced-select>
              <vue-input v-if="element.type == 'No Identificable'" :disabled="viewOnly" class="col-md-6" v-model="element.quantity" label="Cantidad" type="number" name="quantity" :error="form.errorsFor(`elements_id.${index}.quantity`)" placeholder="Cantidad"></vue-input>
              <vue-radio v-if="element.type == 'Identificable'" :disabled="viewOnly" class="col-md-6" v-model="element.entry" :options="siNo" name="entry" :error="form.errorsFor(`elements_id.${index}.entry`)" label="Como desea indresar el código del elemento?" :checked="form.identify_each_element"></vue-radio>
              <vue-input v-if="element.entry == 'Manualmente'" :disabled="viewOnly" class="col-md-12" v-model="element.code" label="Código" type="text" name="code" :error="form.errorsFor(`elements_id.${index}.code`)" placeholder="Código" @onBlur="hashSelected(index)" ></vue-input>
              <vue-advanced-select v-if="element.entry == 'Seleccionarlo'" :disabled="viewOnly" class="col-md-12" v-model="element.code" name="code" label="Código de elemento" placeholder="Seleccione el código" :options="codes[index]" :error="form.errorsFor(`elements_id.${index}.code`)" @selectedName="hashSelected(index)">
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
                      <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeFile(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
              </div>
              <vue-file-simple :disabled="viewOnly" :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/trainingContract/download/${file.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`files.${index}.file`)" :maxFileSize="20"/>
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
      <div>
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
          }
        };
      }
    }
  },
  watch: {
    delivery() {
      this.loading = false;
      this.form = Form.makeFrom(this.delivery, this.method);
    },
    'form.employee_id' () {
      this.updateDetails(`/industrialSecurity/epp/transaction/employeeInfo/${this.form.employee_id}`)
    },
    'form.location_id' () {
      this.uploadElements()
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.delivery, this.method),
      tagsLocationsDataUrl: '/selects/eppLocations',
      elements: [],
      codes: [],
      typesElement: [
        {name: 'Identificable', value: 'Identificable'},
        {name: 'No Identificable', value: 'No Identificable'}
      ],
      elements_position: [],
      postData: {},
      siNo: [
        {text: 'Manualmente', value: 'Manualmente'},
        {text: 'Seleccionarlo', value: 'Seleccionarlo'}
      ],
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

      const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
      if (data != null) {
        this.form.firm_employee = data
      }

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-epps-transactions" });
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
          this.elements_position.splice(0);

          response.data.data.elements.forEach((eleme, key) => {
            this.elements_position.push({
              id_ele: eleme.id_ele,
            })
          })

          this.isLoading = false;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    uploadElements()
    {
      this.isLoading = true;

      this.postData = Object.assign({}, {position_elements: this.elements_position}, {location_id: this.form.location_id});

      axios.post('/industrialSecurity/epp/transaction/eppElementsLocations', this.postData)
      .then(response => {
          this.elements = response.data.multiselect
          this.form.elements_id.splice(0);

          response.data.elements.forEach((eleme, key) => {
            this.form.elements_id.push({
              id_ele: eleme.element.id_ele,
              quantity: eleme.element.quantity,
              code: eleme.element.code,
              type: eleme.element.type
            })
            this.codes[key] = eleme.options
          })

          this.isLoading = false;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    typeElement(index)
    {
      this.isLoading = true;
      axios.post('/industrialSecurity/epp/transaction/elementInfo', {id: this.form.elements_id[index].id_ele, location_id: this.form.location_id})
        .then(response => {
            this.form.elements_id[index].type = response.data.type
            this.codes[index] = response.data.options
        })
        .catch(error => {
            this.isLoading = false;
            Alerts.error('Error', 'Hubo un problema recolectando la información');
        });
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
    addElement() 
    {
      this.form.elements_id.push({
          key: new Date().getTime(),
          id_ele: '',
          quantity: '',
          type: '',
          code: ''
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
