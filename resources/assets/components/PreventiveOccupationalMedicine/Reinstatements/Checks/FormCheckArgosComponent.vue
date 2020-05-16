<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">                                  
    <b-row>
      <b-col>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
          <vue-ajax-advanced-select class="col-md-12" :disabled="viewOnly" v-model="form.employee_id"  name="employee_id" :label="keywordCheck('employee')" placeholder="Seleccione una opción" :url="employeesDataUrl" :selected-object="form.multiselect_employee" :error="form.errorsFor('employee_id')">
                </vue-ajax-advanced-select>

          <center v-if="employeeDetail.id">
            <b-btn variant="primary" size="md" @click="$refs.modalHistorial.show()" ><span class="ion ion-md-eye"></span> Ver otros reportes relacionados con {{ employeeDetail.name }}</b-btn>
          </center>

					<b-modal ref="modalHistorial" :hideFooter="true" id="modals-historial" class="modal-top" size="lg">
						<div slot="modal-title">
							Otros reportes relacionados con {{ employeeDetail.name }}
						</div>

						<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
							<vue-table
                ref="tableEmployee"
                configName="reinstatements-checks-form"
                :customColumnsName="true"
                :params="{ employee_id: employeeDetail.id, current_check_id: check.id }"
                ></vue-table>
						</b-card>
						<br>
						<div class="row float-right pt-12 pr-12y">
							<b-btn variant="primary" @click="$refs.modalHistorial.hide()">Cerrar</b-btn>
						</div>
					</b-modal>
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
          <b-form-row>
            <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.subsystem" label="Subsistema" name="subsystem" :error="form.errorsFor('subsystem')" placeholder=""></vue-textarea>
          </b-form-row>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>

          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="form.has_recommendations" class="col-md-6 offset-md-3" v-model="form.has_recommendations" :options="siNo" name="has_recommendations" :error="form.errorsFor('has_recommendations')" label="¿Tiene recomendaciones?"></vue-radio>
          </b-form-row>
          <div v-show="form.has_recommendations == 'SI'" class="col-md-12">
            <b-form-row>
              <vue-datepicker :disabled="viewOnly" class="col-md-4" v-model="form.start_recommendations" label="Fecha Inicio Recomendaciones" :full-month-name="true" placeholder="Fecha Inicio Recomendaciones" :error="form.errorsFor('start_recommendations')" name="start_recommendations">
                </vue-datepicker>
              <vue-radio :disabled="viewOnly" :checked="form.indefinite_recommendations" class="col-md-4" v-model="form.indefinite_recommendations" :options="siNo" name="indefinite_recommendations" :error="form.errorsFor('indefinite_recommendations')" label="¿Recomendaciones indefinidas?"></vue-radio>
              <vue-datepicker :disabled="viewOnly" v-show="form.indefinite_recommendations == 'NO'" class="col-md-4" v-model="form.end_recommendations" label="Fecha Fin Recomendaciones" :full-month-name="true" placeholder="Fecha Fin Recomendaciones" :error="form.errorsFor('end_recommendations')" name="end_recommendations">
                </vue-datepicker>
            </b-form-row>
          </div>
          <div v-show="form.has_recommendations == 'SI'" class="col-md-12">
            <b-form-row>
              <vue-radio :disabled="viewOnly" :checked="form.relocated" class="col-md-3" v-model="form.relocated" :options="siNo" name="relocated" :error="form.errorsFor('relocated')" label="¿Reubicado?"></vue-radio>
              <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="form.origin_recommendations" :error="form.errorsFor('origin_recommendations')" :multiple="false" :options="originAdvisors" :hide-selected="false" name="origin_recommendations" label="Procedencia de las recomendaciones" placeholder="Seleccione una opción">
                  </vue-advanced-select>
            </b-form-row>
            <b-form-row>
              <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.detail" :label="keywordCheck('detail_recommendations')" name="detail" :error="form.errorsFor('detail')" placeholder=""></vue-textarea>
            </b-form-row>
            <b-form-row>
              <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.conclusion_recommendations" label="Conclusión a recomendaciones" name="conclusion_recommendations" :error="form.errorsFor('conclusion_recommendations')" placeholder=""></vue-textarea>
            </b-form-row>
          </div>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>

          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="form.has_restrictions" class="col-md-6 offset-md-3" v-model="form.has_restrictions" :options="siNo" name="has_restrictions" :error="form.errorsFor('has_restrictions')" label="¿Tiene Restricción?"></vue-radio>
          </b-form-row>
          <b-form-row v-show="form.has_restrictions == 'SI'">
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="form.restriction_id" :error="form.errorsFor('restriction_id')" :selected-object="form.multiselect_restriction" name="restriction_id" label="Parte del cuerpo afectada" placeholder="Seleccione una opción" :url="restrictionsDataUrl"> </vue-ajax-advanced-select>
          </b-form-row>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>
          
          <b-form-row>
            <div class="col-md-6">
                <monitoring-medical-selector :disabled="viewOnly" :options="medicalConclusions" ref="medicalMonitoring" :monitoring-registered="check.medical_monitorings" :siNo="siNo">
                    <template slot="monitoring-label">Fecha Seguimiento Médico</template>
                    <template slot="conclusion-label">Conclusión Seguimiento Médico</template>
                </monitoring-medical-selector>
            </div>

            <div class="col-md-6">
                <monitoring-laboral-selector :disabled="viewOnly" :options="laborConclusions" ref="laborMonitoring" :monitoring-registered="check.labor_monitorings" :siNo="siNo">
                    <template slot="monitoring-label">Fecha Seguimiento Laboral</template>
                    <template slot="conclusion-label">Conclusión Seguimiento Laboral</template>
                </monitoring-laboral-selector>
            </div>
          </b-form-row>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px; padding-top: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <b-form-row>
                <vue-radio :disabled="viewOnly" :checked="form.in_process_origin" class="col-md-6" v-model="form.in_process_origin" :options="siNo" name="in_process_origin" :error="form.errorsFor('in_process_origin')" label="¿En proceso de calificación de origen?"></vue-radio>

                <vue-radio v-show="form.in_process_origin == 'NO'" :disabled="viewOnly" :checked="form.process_origin_done" class="col-md-6" v-model="form.process_origin_done" :options="siNo" name="process_origin_done" :error="form.errorsFor('process_origin_done')" label="¿Ya se hizo el proceso de calificación de origen?"></vue-radio>

                <vue-datepicker :disabled="viewOnly" v-show="form.in_process_origin == 'NO' && form.process_origin_done == 'SI'" class="col-md-6" v-model="form.process_origin_done_date" label="Fecha proceso calificación origen" :full-month-name="true" :error="form.errorsFor('process_origin_done_date')" name="process_origin_done_date">
                </vue-datepicker>

                <vue-advanced-select v-show="showEmitterOrigin" :disabled="viewOnly" class="col-md-6" v-model="form.emitter_origin" :error="form.errorsFor('emitter_origin')" :multiple="false" :options="originEmitters" :hide-selected="false" name="emitter_origin" label="Entidad que Califica Origen" placeholder="Seleccione una opción">
                    </vue-advanced-select>

                <vue-file-simple v-show="form.in_process_origin == 'NO' && form.process_origin_done == 'SI'" :help-text="form.old_process_origin_file ? `Para descargar el archivo actual, haga click <a href='/biologicalmonitoring/reinstatements/check/downloadOriginFile/${form.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" accept=".pdf" class="col-md-12" v-model="form.process_origin_file" label="Adjuntar PDF" name="process_origin_file" :error="form.errorsFor('process_origin_file')" placeholder="Seleccione un archivo" maxFileSize="20"></vue-file-simple>
                
              </b-form-row>
            </div>
            <div class="col-md-6">
              <b-form-row>
                <vue-radio :disabled="viewOnly" :checked="form.in_process_pcl" class="col-md-6" v-model="form.in_process_pcl" :options="siNo" name="in_process_pcl" :error="form.errorsFor('in_process_pcl')" label="¿En proceso de PCL?"></vue-radio>

                <vue-radio v-show="form.in_process_pcl == 'NO'" :disabled="viewOnly" :checked="form.process_pcl_done" class="col-md-6" v-model="form.process_pcl_done" :options="siNo" name="process_pcl_done" :error="form.errorsFor('process_pcl_done')" label="¿Ya se hizo el proceso de PCL?"></vue-radio>

                <vue-datepicker :disabled="viewOnly" v-show="form.in_process_pcl == 'NO' && form.process_pcl_done == 'SI'" class="col-md-6" v-model="form.process_pcl_done_date" label="Fecha proceso PCL" :full-month-name="true" :error="form.errorsFor('process_pcl_done_date')" name="process_pcl_done_date">
                  </vue-datepicker>

                <vue-input :disabled="viewOnly" class="col-md-6" v-show="showPcl" v-model="form.pcl" label="Calificación PCL" type="number" name="pcl" :error="form.errorsFor('pcl')"></vue-input>

                <vue-input :disabled="viewOnly" class="col-md-6 offset-md-6" v-show="showPcl" v-model="form.entity_rating_pcl" label="Entidad que califica PCL" type="text" name="entity_rating_pcl" :error="form.errorsFor('entity_rating_pcl')"></vue-input>

                <vue-file-simple v-show="form.in_process_pcl == 'NO' && form.process_pcl_done == 'SI'" :help-text="form.old_process_pcl_file ? `Para descargar el archivo actual, haga click <a href='/biologicalmonitoring/reinstatements/check/downloadPclFile/${form.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" accept=".pdf" class="col-md-12" v-model="form.process_pcl_file" label="Adjuntar PDF" name="process_pcl_file" :error="form.errorsFor('process_pcl_file')" placeholder="Seleccione un archivo"></vue-file-simple>

              </b-form-row>
            </div>
          </div>

          <div class="col-md-12" style="padding-left: 15px; padding-right: 15px;">
            <hr class="border-dark container-m--x mt-0 mb-4">
          </div>

          <b-form-row>
            <div class="col-md-12">
              <tracing-inserter
                :label="keywordCheck('tracings')"
                :disabled="viewOnly"
                :editable-tracings="auth.can['reinc_checks_manage_tracings']"
                :old-tracings="check.oldTracings"
                :si-no="siNo"
                :check-id="check.id"
                ref="tracingInserter"
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
import MonitoringMedicalSelector from './MonitoringMedicalSelector.vue';
import MonitoringLaboralSelector from './MonitoringLaboralSelector.vue';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import TracingInserter from './TracingInserter.vue';

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
    VueTextarea,
    MonitoringMedicalSelector,
    MonitoringLaboralSelector,
    VueFileSimple,
    TracingInserter
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
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
          conclusion_recommendations: '',
          subsystem: '',
          
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
    },
    showEmitterOrigin() {
      if (this.form.in_process_origin == 'SI')
        return true;

      if (this.form.in_process_origin == 'NO' && this.form.process_origin_done == 'SI')
        return true;

      return false;
    },
    showPcl() {
      if (this.form.in_process_pcl == 'SI')
        return true;

      if (this.form.in_process_pcl == 'NO' && this.form.process_pcl_done == 'SI')
        return true;

      return false;
    }
  },
  mounted() {
    if (this.form.cie10_code_id)
      this.updateDetails(`/biologicalmonitoring/reinstatements/cie10/${this.form.cie10_code_id}`, 'cie10CodeDetail');
    
    if (this.form.employee_id)
    {
      this.updateDetails(`/administration/employee/${this.form.employee_id}`, 'employeeDetail')
    }

    if (!this.isEdit && !this.viewOnly)
    {
      this.form.relocated = 'NO';
      this.form.indefinite_recommendations = 'SI';
    }

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
    };
  },
  methods: {
    submit(e) {

      this.loading = true;

      this.form.clearFilesBinary();

      this.form.files.forEach((file, keyFile) => {
        this.form.addFileBinary(`${keyFile}`, file.file)
      });

      if (!this.$refs.medicalMonitoring.monitoringListIsValid()) {
        Alerts.error('Error', 'Hay un campo vacío en la lista de monitoreo médico');
        return;
      }

      if (!this.$refs.laborMonitoring.monitoringListIsValid()) {
        Alerts.error('Error', 'Hay un campo vacío en la lista de monitoreo laboral');
        return;
      }

      this.form.medical_monitorings = this.$refs.medicalMonitoring.getMonitoringList();
      this.form.labor_monitorings = this.$refs.laborMonitoring.getMonitoringList();
      this.form.new_tracing = this.$refs.tracingInserter.getNewTracing();
      this.form.oldTracings = this.$refs.tracingInserter.getOldTracings();
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "reinstatements-checks" });
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
  }
};
</script>
