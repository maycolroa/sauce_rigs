<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">                                  
    <b-row>
      <b-col>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
          <vue-ajax-advanced-select class="col-md-12" :disabled="viewOnly" v-model="form.employee_id"  name="employee_id" :label="keywordCheck('employee')" placeholder="Seleccione una opción" :url="employeesDataUrl" :selected-object="form.multiselect_employee" :error="form.errorsFor('employee_id')">
                </vue-ajax-advanced-select>
                <br><br>
                <center>
                  <div v-show="showMessage" class="fixedFooter">
                      <h6 style="text-color: white; text-align: center;">{{ message }}</h6>
                  </div>
                </center>
        </b-card>
      </b-col>
    </b-row>
    <b-row>
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
    </b-row>

    <b-row>
      <b-col>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
          <b-form-row>
            <vue-advanced-select :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="form.disease_origin" :error="form.errorsFor('disease_origin')" :multiple="false" :options="diseaseOrigins" :hide-selected="false" name="disease_origin" :label="keywordCheck('disease_origin')" placeholder="Seleccione una opción">
                </vue-advanced-select>
          </b-form-row>
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.cie10_code_id" :error="form.errorsFor('cie10_code_id')" :selected-object="form.multiselect_cie10Code" name="cie10_code_id" label="Código CIE 10" placeholder="Seleccione una opción" :url="cie10CodesDataUrl"> </vue-ajax-advanced-select>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="true" class="col-md-6" v-model="cie10CodeDetail.system" label="Sistema" type="text" name="system"></vue-input>
            <vue-input :disabled="true" class="col-md-6" v-model="cie10CodeDetail.category" label="Categoría" type="text" name="category"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-advanced-select :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="form.laterality" :error="form.errorsFor('laterality')" :multiple="false" :options="lateralities" :hide-selected="false" name="laterality" label="Lateralidad" placeholder="Seleccione una opción">
                </vue-advanced-select>
          </b-form-row>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>
          
          <b-form-row>
            <div class="col-md-12">
                <monitoring-selector :disabled="viewOnly" :options="medicalConclusions" ref="medicalMonitoring" :monitoring-registered="check.medical_monitorings">
                    <template slot="monitoring-label">Fecha Seguimiento Médico</template>
                    <template slot="conclusion-label">Conclusión Seguimiento Médico</template>
                </monitoring-selector>
            </div>
          </b-form-row>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px; padding-top: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>
          
          <b-form-row>
            <div class="col-md-12">
                <monitoring-selector :disabled="viewOnly" :options="laborConclusions" ref="laborMonitoring" :monitoring-registered="check.labor_monitorings">
                    <template slot="monitoring-label">Fecha Seguimiento Laboral</template>
                    <template slot="conclusion-label">Conclusión Seguimiento Laboral</template>
                </monitoring-selector>
            </div>
          </b-form-row>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>

          <b-form-row>
            <div class="col-md-12">
              <tracing-inserter
                :label="keywordCheck('labor_notes')"
                :generate-pdf="false"
                :disabled="viewOnly"
                :editable-tracings="auth.can['reinc_checks_manage_tracings']"
                :old-tracings="check.oldLaborNotes"
                :si-no="siNo"
                ref="laborNotesInserter"
              >
              </tracing-inserter>
            </div>
          </b-form-row>
          
        </b-card>
      </b-col>
    </b-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import MonitoringSelector from './MonitoringSelector.vue';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import TracingInserter from './TracingInserter.vue';
import TracingOtherCheck from './TracingOtherCheck.vue';
import FilesMultiple from './FilesMultiple.vue';

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
    VueTextarea,
    MonitoringSelector,
    VueFileSimple,
    TracingInserter,
    FilesMultiple,
    TracingOtherCheck
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: true },
    employeesDataUrl: { type: String, default: "" },
    regionalsDataUrl: { type: String, default: "" },
    headquartersDataUrl: { type: String, default: "" },
    areasDataUrl: { type: String, default: "" },
    processesDataUrl: { type: String, default: "" },
    positionsDataUrl: { type: String, default: "" },
    cie10CodesDataUrl: { type: String, default: "" },
    epsDataUrl: { type: String, default: "" },
    restrictionsDataUrl: { type: String, default: "" },
    disableWacthSelectInCreated: { type: Boolean, default: false},
    tracingOthersUrl: { type: String, default: "" },
    diseaseOrigins: {
      type: Array,
      default: function() {
        return [];
      }
    },
    lateralities: {
      type: Array,
      default: function() {
        return [];
      }
    },
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    originAdvisors: {
      type: Array,
      default: function() {
        return [];
      }
    },
    refundClassification: {
      type: Array,
      default: function() {
        return [];
      }
    },
    medicalConclusions: {
      type: Array,
      default: function() {
        return [];
      }
    },
    laborConclusions: {
      type: Array,
      default: function() {
        return [];
      }
    },
    originEmitters: {
      type: Array,
      default: function() {
        return [];
      }
    },
    typeQualificationControversy: {
      type: Array,
      default: function() {
        return [];
      }
    },
    clasificationOrigin: {
      type: Array,
      default: function() {
        return [];
      }
    },
    check: {
      default() {
        return {
          employee_id: '',
          disease_origin: '',
          has_recommendations: '',
          start_recommendations: '',
          end_recommendations: '',
          indefinite_recommendations: '',
          origin_recommendations: '',
          relocated: '',
          laterality: '',
          detail: '',
          monitoring_recommendations: '',
          in_process_origin: '',
          process_origin_done: '',
          process_origin_done_date: '',
          emitter_origin: '',
          qualification_origin: '',
          type_qualification_origin: '',
          is_firm_process_origin: '',
          in_process_pcl: '',
          process_pcl_done: '',
          process_pcl_done_date: '',
          pcl: '',
          entity_rating_pcl: '',
          is_firm_process_pcl: '',
          process_origin_file: '',
          process_origin_file_name: '',
          process_pcl_file: '',
          process_pcl_file_name: '',
          cie10_code_id: '',
          restriction_id: '',
          has_restrictions: '',
          relocated_regional_id: '',
          relocated_headquarter_id: '',
          relocated_process_id: '',
          relocated_position_id: '',
          date_controversy_origin_1: '',
          date_controversy_origin_2: '',
          date_controversy_pcl_1: '',
          date_controversy_pcl_2: '',
          emitter_controversy_origin_1: '',
          emitter_controversy_origin_2: '',
          qualification_controversy_1: '',
          qualification_controversy_2: '',
          is_firm_controversy_1: '',
          is_firm_controversy_pcl_1: '',
          type_controversy_origin_1: '',
          type_controversy_origin_2: '',
          emitter_controversy_pcl_1: '',
          emitter_controversy_pcl_2: '',
          punctuation_controversy_plc_1: '',
          punctuation_controversy_plc_2: '',
          malady_origin: '',
          eps_favorability_concept: '',
          case_classification: '',
          relocated_date: '',
          start_restrictions: '',
          end_restrictions: '',
          indefinite_restrictions: '',
          has_incapacitated: '',
          incapacitated_days: '',
          incapacitated_last_extension: '',
          deadline: '',
          next_date_tracking: '',
          sve_associated: '',
          medical_certificate_ueac: '',
          relocated_type: '',
          created_at: '',
          refundClassification: '',
          start_incapacitated: '',
          end_incapacitated: '',
          
          new_tracing: [],
          oldTracings: [],
          medical_monitorings: [],
          labor_monitorings: [],
          new_labor_notes: [],
          oldLaborNotes: [],
          files: []
        };
      }
    }
  },
  watch: {
    check() {
      this.form = Form.makeFrom(this.check, this.method);
    },
    'form.employee_id' () {
      this.updateDetails(`/administration/employee/${this.form.employee_id}`, 'employeeDetail')
      //this.updateTracingOtherReport('sau_reinc_tracings', 'tracingOtherReport');
      //this.updateTracingOtherReport('sau_reinc_labor_notes', 'laborNotesOtherReport');
    },
    'form.cie10_code_id': function() {
      this.updateDetails(`/biologicalmonitoring/reinstatements/cie10/${this.form.cie10_code_id}`, 'cie10CodeDetail');
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
  mounted() {
    if (this.form.cie10_code_id)
      this.updateDetails(`/biologicalmonitoring/reinstatements/cie10/${this.form.cie10_code_id}`, 'cie10CodeDetail');
    
    if (this.form.employee_id)
    {
      this.updateDetails(`/administration/employee/${this.form.employee_id}`, 'employeeDetail')
      //this.updateTracingOtherReport('sau_reinc_labor_notes', 'laborNotesOtherReport');
    }

    this.getMessageIncapacitate();

    setTimeout(() => {
      this.disableWacth = false
    }, 3000)
    
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.check, this.method),
      employeeDetail: [],
      cie10CodeDetail: [],
      disabledDates: {
        from: new Date()
      },
      empty: {
        headquarter: false,
        process: false
      },
      disableWacth: this.disableWacthSelectInCreated,
      tracingOtherReport: [],
      laborNotesOtherReport: [],
      message: '',
      showMessage: false
    };
  },
  methods: {
    formatDate(param)
    {
      let date = ''

      if (param)
        date = new Date(param).toLocaleDateString()

      return date
    },
    updateEmptyKey(keyEmpty)
    {
      this.empty[keyEmpty]  = false
    },
    emptySelect(keySelect, keyEmpty)
    {
      if (this.form[keySelect] !== '' && !this.disableWacth)
      {
        this.empty[keyEmpty] = true
        this.form[keySelect] = ''
      }
    },
    getMessageIncapacitate()
    {
      if (this.isEdit || this.viewOnly)
      {
        axios.post('/biologicalmonitoring/reinstatements/getMessageIncapacitate', {check_id: this.form.id})
          .then(response => {
            this.message = response.data;
            if (this.message)
              this.showMessage = true;
          })
          .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          });
      }
    },
    updateDetails(url, key)
    {
      this.isLoading = true;
      axios.get(url)
      .then(response => {
          this[key] = response.data.data;

          if(key == 'employeeDetail' && !this.isEdit)
          {
            //Quemado para cambiar el valor de los campos de reubicacion
            this.disableWacth = true
            this.form.relocated_position_multiselect = this.employeeDetail.multiselect_cargo;
            this.form.relocated_regional_multiselect = this.employeeDetail.multiselect_regional;
            this.form.relocated_headquarter_multiselect = this.employeeDetail.multiselect_sede;
            this.form.relocated_process_multiselect = this.employeeDetail.multiselect_proceso;

            this.form.relocated_position_id = this.employeeDetail.employee_position_id
            this.form.relocated_regional_id = this.employeeDetail.employee_regional_id
            this.form.relocated_headquarter_id = this.employeeDetail.employee_headquarter_id
            this.form.relocated_process_id = this.employeeDetail.employee_process_id

            if (this.$refs.tableEmployee !== undefined)
              this.$refs.tableEmployee.refresh()
          }

          this.isLoading = false;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
    },
  }
};
</script>


<style scoped>

.fixedFooter {
    padding: 10px;
    background: #f0635f;
    color: #fff;
    border: 2px solid #aaa5a6;
    border-radius: 8px;
    margin-bottom: 5px;
    width: 300px;
}

</style>