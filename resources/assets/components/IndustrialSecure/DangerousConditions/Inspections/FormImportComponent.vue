<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-body>
        <p><b>Estimado usuario las opciones seleccionadas se asociaran a todas las inspecciones del listado</b></p>
        <b-form-row>
            <vue-ajax-advanced-select v-if="locationForm.regional == 'SI'" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regionals')" placeholder="Seleccione las opciones" :url="regionalsDataUrl" :multiple="true" :allowEmpty="true">
                </vue-ajax-advanced-select>
            <vue-ajax-advanced-select v-if="locationForm.headquarter == 'SI'" :disabled="form.employee_regional_id && form.employee_regional_id.length == 0" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_sede" name="employee_headquarter_id" :label="keywordCheck('headquarters')" placeholder="Seleccione las opciones" :url="headquartersDataUrl" :parameters="{regional: form.employee_regional_id }" :multiple="true" :allowEmpty="true">
                </vue-ajax-advanced-select>
            
            <vue-ajax-advanced-select v-if="locationForm.process == 'SI'" :disabled="form.employee_headquarter_id &&form.employee_headquarter_id.length == 0" class="col-md-6" v-model="form.employee_process_id" :error="form.errorsFor('employee_process_id')" :selected-object="form.multiselect_proceso" name="employee_process_id" :label="keywordCheck('processes')" placeholder="Seleccione las opciones" :url="processesDataUrl" :parameters="{headquarter: form.employee_headquarter_id }" :multiple="true" :allowEmpty="true">
            </vue-ajax-advanced-select>
            <vue-ajax-advanced-select v-if="locationForm.area == 'SI'" :disabled="form.employee_process_id && form.employee_process_id.length == 0" class="col-md-6" v-model="form.employee_area_id" :error="form.errorsFor('employee_area_id')" :selected-object="form.multiselect_area" name="employee_area_id" :label="keywordCheck('areas')" placeholder="Seleccione las opciones" :url="areasDataUrl" :parameters="{process: form.employee_process_id, headquarter: form.employee_headquarter_id }" :multiple="true" :allowEmpty="true">
            </vue-ajax-advanced-select>
          </b-form-row>
        <b-form-row>
          <vue-file-simple class="col-md-12" v-model="form.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`file`)" :maxFileSize="20"/>
        </b-form-row>
      </b-card-body>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Importar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";

export default {
  components: {
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    regionalsDataUrl: { type: String, default: "" },
    headquartersDataUrl: { type: String, default: "" },
    areasDataUrl: { type: String, default: "" },
    processesDataUrl: { type: String, default: "" },
    inspection: {
      default() {
        return {
          employee_regional_id: [],
          employee_headquarter_id: [],
          employee_process_id: [],
          employee_area_id: [],
          file: ''
        };
      }
    }
  },
  watch: {
    inspection() {
      this.loading = false;
      this.form = Form.makeFrom(this.inspection, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.inspection, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
          this.$router.push({ name: "dangerousconditions-inspections" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
}
</script>
