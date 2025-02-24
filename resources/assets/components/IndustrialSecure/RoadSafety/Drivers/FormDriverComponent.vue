<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly || isEdit" class="col-md-12" v-model="form.employee_id" :error="form.errorsFor('employee_id')" :selected-object="form.multiselect_employee" name="employee_id" :label="keywordCheck('employee')" placeholder="Seleccione una opción" :url="employeesDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <b-col>
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
                  <div v-if="employeeDetail.regional"><b>{{ keywordCheck('regional') }}:</b> {{ employeeDetail.regional.name }}</div>
                  <div v-if="employeeDetail.headquarter"><b>{{ keywordCheck('headquarter') }}:</b> {{  employeeDetail.headquarter.name }}</div>
                  <div v-if="employeeDetail.process"><b>{{ keywordCheck('process') }}:</b> {{ employeeDetail.process.name }}</div>
                  <div v-if="employeeDetail.area"><b>{{ keywordCheck('area') }}:</b> {{ employeeDetail.area.name }}</div>
                  <div><b>{{ keywordCheck('eps') }}:</b> {{ employeeDetail.eps ? `${employeeDetail.eps.code} - ${employeeDetail.eps.name}` : '' }}</div>
              </b-col>
          </b-row>
        </b-card>
      </b-col>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.type_license_id" name="type_license" :error="form.errorsFor('type_license_id')" label="Tipo de licencia" placeholder="Seleccione el tipo" :url="typeLicenseDataUrl" :multiple="false" :allowEmpty="true" :selected-object="form.multiselect_type_license">
      </vue-ajax-advanced-select>
      <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date_license" label="Vigencia de licencia" :full-month-name="true" placeholder="Vigencia de licencia" :error="form.errorsFor('date_license')" name="date_license">
                </vue-datepicker>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.vehicle_id" :error="form.errorsFor('vehicle_id')" :selected-object="form.multiselect_vehicle" name="vehicle_id" label="Vehiculo" placeholder="Seleccione una opción" :url="vehiclesDataUrl" :multiple="true">
          </vue-ajax-advanced-select>
        <label v-if="vehicleReasigned.asigned" >Este vehiculo de placa {{ vehicleReasigned.plate }} ya está asignado al conductor {{ vehicleReasigned.driver }}</label>

        <vue-radio v-if="vehicleReasigned.asigned" :disabled="viewOnly" :checked="vehicles_reasigned" class="col-md-12" v-model="vehicles_reasigned" :options="siNo" name="vehicles_reasigned" :error="form.errorsFor('vehicles_reasigned')" label="¿Desea asignarlo de todas formas?"></vue-radio>
    </b-form-row>

    <b-form-row>
      <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-12" v-model="form.responsible" name="responsible" :error="form.errorsFor('responsible')" label="Responsable del conductor" placeholder="Seleccione el responsable" :url="tagsResponsibleDriverDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
      </vue-ajax-advanced-select-tag-unic>
    </b-form-row>

    <div class="col-md-12">      
      <blockquote class="blockquote text-center">
            <p class="mb-0">Documentos</p>
      </blockquote>

      <template v-for="(document, index) in form.documents">
        <b-card no-body class="mb-2" style="width: 100%;">
          <b-form-row :key="document.key">
            <vue-file-simple :disabled="viewOnly" :help-text="document.id ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/roadsafety/drivers/download/${document.id}' target='blank'>aqui</a> ` : null" class="col-md-6" v-model="document.file" :label="document.name" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`documents.${index}.file`)" :maxFileSize="20"/>
            <vue-radio :disabled="viewOnly" class="col-md-2" v-model="document.required_expiration_date" :options="siNo" :name="`siNo${index}`" label="¿Tiene fecha de vencimiento?" :checked="document.required_expiration_date">
              </vue-radio>
            <vue-datepicker v-if="document.required_expiration_date == 'SI'" :disabled="viewOnly" class="col-md-4" v-model="document.expiration_date" label="Fecha de vencimiento" :full-month-name="true" placeholder="Fecha de vencimiento" :error="form.errorsFor(`documents.${index}.expiration_date`)" name="date_license">
                </vue-datepicker>
          </b-form-row>
        </b-card>
      </template>
    </div>

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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import Form from "@/utils/Form.js";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Alerts from '@/utils/Alerts.js';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueAjaxAdvancedSelectTagUnic,
    VueRadio,
    VueFileSimple

  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    driver: {
      default() {
        return {
            employee_id: '',
            responsible: '',
            type_license_id: '',
            date_license: '',
            vehicle_id: '',
            documents: [],
        };
      }
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
  watch: {
    driver() {
      this.loading = false;
      this.form = Form.makeFrom(this.driver, this.method);
    },
    'form.employee_id' () {
      this.updateDetails(`/administration/employee/${this.form.employee_id}`, 'employeeDetail')
    },
    'form.vehicle_id' () {
      this.updateDetails(`/administration/employee/${this.form.employee_id}`, 'employeeDetail')
    },
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.driver, this.method),
      employeeDetail: [],
      vehicles_reasigned: '',
      vehicleReasigned: '',
      vehicles_reasigned: '',
      employeesDataUrl: "/selects/employees",
      vehiclesDataUrl: "/selects/vehicles",
      typeLicenseDataUrl: "/selects/tagTypeLicense",
      tagsResponsibleDriverDataUrl: "/selects/tagResponsibleDriver",
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
    };
  },
  methods: {
    addDocument() {
        this.form.documents.push({
            key: new Date().getTime(),
            name: ''
        })
    },
    removeDocument(index)
    {
      if (this.form.documents[index].id != undefined)
        this.form.delete.documents.push(this.form.documents[index].id)

      this.form.documents.splice(index, 1)
    },
    submit(e) {
      this.loading = true;

      this.form.clearFilesBinary();
      _.forIn(this.form.documents, (file, keyFile) => {
        if (file.file)
          this.form.addFileBinary(`${keyFile}`, file.file);
      });

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-roadsafety-drivers" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    updateDetails(url, key)
    {
      this.isLoading = true;
      axios.get(url)
      .then(response => {
          this[key] = response.data.data;

          this.getDocuments(this[key]);

          this.isLoading = false;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    getDocuments(employee)
    {
      if (!this.isEdit && !this.viewOnly)
      {
        let postData = Object.assign({}, {position_id: employee.employee_position_id});
        this.isLoading = true;
        axios.post('/industrialSecurity/roadsafety/drivers/getDocuments', postData)
        .then(response => {
            this.form.documents = response.data.data;
            this.isLoading = false;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
      }
    },
    getStateVehicle()
    {
      let postData = Object.assign({}, {vehicle_id: this.form.vehicle_id});
      this.isLoading = true;
      axios.post('/industrialSecurity/roadsafety/drivers/getVehicle', postData)
      .then(response => {
        console.log(response.data.data)
          this.vehicleReasigned = response.data.data;
          this.isLoading = false;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    formatDate(param)
    {
      let date = ''

      if (param)
        date = new Date(param).toLocaleDateString()

      return date
    },
  }
};
</script>
