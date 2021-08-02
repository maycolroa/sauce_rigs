<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <form-wizard ref="wizardFormAudiometry">
      <!-- Allow html in tab title (this template required for the proper styling) -->
      <template slot="step" slot-scope="props">
        <wizard-step :tab="props.tab" @click.native="props.navigateToTab(props.index)" @keyup.enter.native="props.navigateToTab(props.index)" :transition="props.transition" :index="props.index">
          <span slot="title" :class="{'text-danger':props.tab.validationError}" v-html="props.tab.title"></span>
        </wizard-step>
      </template>
      <!-- / -->
      <tab-content v-if="viewOnly" title="Resultado">
        <div class="row">

          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="primary" title="Oido Derecho" class="mb-3 box-shadow-none">
              <results-audiometry :gap="form.gap_right"
                                  :air-pta="form.air_right_pta"
                                  :osseous-pta="form.osseous_right_pta"
                                  :severity-grade-air-pta="form.severity_grade_air_right_pta"
                                  :severity-grade-osseous-pta="form.severity_grade_osseous_right_pta"
                                  :severity-grade-air4000="form.severity_grade_air_right_4000"
                                  :severity-grade-osseous4000="form.severity_grade_osseous_right_4000"
                                  :severity-grade-air6000="form.severity_grade_air_right_6000"
                                  :severity-grade-air8000="form.severity_grade_air_right_8000"/>
            </b-card>

          </div>
          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="secondary" title="Oido izquierdo" class="mb-3 box-shadow-none">
              <results-audiometry :gap="form.gap_left"
                                  :air-pta="form.air_left_pta"
                                  :osseous-pta="form.osseous_left_pta"
                                  :severity-grade-air-pta="form.severity_grade_air_left_pta"
                                  :severity-grade-osseous-pta="form.severity_grade_osseous_left_pta"
                                  :severity-grade-air4000="form.severity_grade_air_left_4000"
                                  :severity-grade-osseous4000="form.severity_grade_osseous_left_4000"
                                  :severity-grade-air6000="form.severity_grade_air_left_6000"
                                  :severity-grade-air8000="form.severity_grade_air_left_8000"/>
            </b-card>

          </div>
        </div>
        <div class="row">
            <div class="col-lg">
              <b-card bg-variant="transparent" border-variant="dark" class="mb-12 box-shadow-none">
                <div><b>Base:</b> {{ form.base_type == 'Base' ? 'Si' : 'No'}}</div>
                <div v-if="form.base"><router-link :to="{ path: `/preventiveoccupationalmedicine/biologicalmonitoring/audiometry/view/${form.base}` }">Ver Audiometria</router-link></div>
                <div v-if="form.base_state"><b>Tipo Base:</b> {{ form.base_state }}</div>
              </b-card>
            </div>
        </div>
        <br>
      </tab-content>
      <tab-content title="General">
        <b-form-row>
          <location-level-component
            :is-edit="isEdit"
            :view-only="viewOnly"
            v-model="form.locations"
            :location-level="audiometry.locations"
            :form="form"
            application="preventiveoccupationalmedicine"
            module="audiometry"/>
        </b-form-row>
        <b-form-row>
          <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date" label="Fecha" :full-month-name="true" placeholder="Seleccione la fecha" :error="form.errorsFor('date')" name="date" :disabled-dates="disabledDates">
          </vue-datepicker>

          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_id" :error="form.errorsFor('employee_id')" :selected-object="form.multiselect_employee" name="employee_id" :label="keywordCheck('employee')" placeholder="Seleccione una opción" :url="employeesDataUrl">
          </vue-ajax-advanced-select>
        </b-form-row>
        <vue-textarea :disabled="viewOnly" v-model="form.previews_events" label="Eventos previos" :error="form.errorsFor('previews_events')" name="previews_events" placeholder="Eventos previos"></vue-textarea>

        <b-form-row>
          <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.exposition_level" :error="form.errorsFor('exposition_level')" :multiple="false" :options="expositionLevel" :hide-selected="false" name="exposition_level" label="Nivel de exposicion (Disometría)" placeholder="Seleccione el nivel de exposicion (Disometría)">
          </vue-advanced-select>
          <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.epp" :error="form.errorsFor('epp')" :multiple="true" :options="epp" name="epp" label="Elementos de protección personal" placeholder="Seleccione los elementos de protección personal">
          </vue-advanced-select>
        </b-form-row>
        <vue-textarea :disabled="viewOnly" v-model="form.recommendations" label="Conducta Tomada" :error="form.errorsFor('recommendations')" name="recommendations" placeholder="Conducta Tomada"></vue-textarea>

        <vue-textarea :disabled="viewOnly" v-model="form.observation" label="Observación general" :error="form.errorsFor('observation')" name="observation" placeholder="Observación general"></vue-textarea>
      </tab-content>
      <tab-content title="Via aerea">
        <div class="row">

          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="primary" title="Derecha Aereo" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.air_right_500" label="500 Hz" type="number" name="air_right_500" :error="form.errorsFor('air_right_500')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.air_right_1000" label="1000 Hz" type="number" name="air_right_1000" :error="form.errorsFor('air_right_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.air_right_2000" label="2000 Hz" type="number" name="air_right_2000" :error="form.errorsFor('air_right_2000')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.air_right_3000" label="3000 Hz" type="number" name="air_right_3000" :error="form.errorsFor('air_right_3000')" placeholder="0"></vue-input>
                </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.air_right_4000" label="4000 Hz" type="number" name="air_right_4000" :error="form.errorsFor('air_right_4000')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.air_right_6000" label="6000 Hz" type="number" name="air_right_6000" :error="form.errorsFor('air_right_6000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
              <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.air_right_8000" label="8000 Hz" type="number" name="air_right_8000" :error="form.errorsFor('air_right_8000')" placeholder="0"></vue-input>
              <b-col></b-col>
              </b-form-row>
            </b-card>

          </div>
          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="secondary" title="Izquierda Aereo" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.air_left_500" label="500 Hz" type="number" name="air_left_500" :error="form.errorsFor('air_left_500')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.air_left_1000" label="1000 Hz" type="number" name="air_left_1000" :error="form.errorsFor('air_left_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.air_left_2000" label="2000 Hz" type="number" name="air_left_2000" :error="form.errorsFor('air_left_2000')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.air_left_3000" label="3000 Hz" type="number" name="air_left_3000" :error="form.errorsFor('air_left_3000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.air_left_4000" label="4000 Hz" type="number" name="air_left_4000" :error="form.errorsFor('air_left_4000')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.air_left_6000" label="6000 Hz" type="number" name="air_left_6000" :error="form.errorsFor('air_left_6000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.air_left_8000" label="8000 Hz" type="number" name="air_left_8000" :error="form.errorsFor('air_left_8000')" placeholder="0"></vue-input>
                <b-col></b-col>
              </b-form-row>
            </b-card>

          </div>
        </div>
      </tab-content>
      <tab-content title="Via osea">
        <div class="row">

          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="primary" title="Derecha Oseo" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.osseous_right_500" label="500 Hz" type="number" name="osseous_right_500" :error="form.errorsFor('osseous_right_500')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.osseous_right_1000" label="1000 Hz" type="number" name="osseous_right_1000" :error="form.errorsFor('osseous_right_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.osseous_right_2000" label="2000 Hz" type="number" name="osseous_right_2000" :error="form.errorsFor('osseous_right_2000')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.osseous_right_3000" label="3000 Hz" type="number" name="osseous_right_3000" :error="form.errorsFor('osseous_right_3000')" placeholder="0"></vue-input>
                </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.osseous_right_4000" label="4000 Hz" type="number" name="osseous_right_4000" :error="form.errorsFor('osseous_right_4000')" placeholder="0"></vue-input>
                <b-col></b-col>
              </b-form-row>
            </b-card>

          </div>
          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="secondary" title="Izquierda Oseo" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.osseous_left_500" label="500 Hz" type="number" name="osseous_left_500" :error="form.errorsFor('osseous_left_500')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.osseous_left_1000" label="1000 Hz" type="number" name="osseous_left_1000" :error="form.errorsFor('osseous_left_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.osseous_left_2000" label="2000 Hz" type="number" name="osseous_left_2000" :error="form.errorsFor('osseous_left_2000')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.osseous_left_3000" label="3000 Hz" type="number" name="osseous_left_3000" :error="form.errorsFor('osseous_left_3000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.osseous_left_4000" label="4000 Hz" type="number" name="osseous_left_4000" :error="form.errorsFor('osseous_left_4000')" placeholder="0"></vue-input>
                <b-col></b-col>
              </b-form-row>
            </b-card>

          </div>
        </div>
      </tab-content>


      <template slot="footer" slot-scope="props">
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>
        <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
        <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>

    </form-wizard>

  </b-form>
</template>

<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import ResultsAudiometry from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/ResultsAudiometryComponent.vue';
import Form from "@/utils/Form.js";
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput,
    VueDatepicker,
    VueAdvancedSelect,
    VueTextarea,
    FormWizard,
    TabContent,
    WizardStep,
    ResultsAudiometry,
    LocationLevelComponent
  },
  mounted() {
    this.$refs.wizardFormAudiometry.activateAll();
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    epp: {
      type: Array,
      default: function() {
        return [];
      }
    },
    expositionLevel: {
      type: Array,
      default: function() {
        return [];
      }
    },
    employeesDataUrl: { type: String, default: "" },
    audiometry: {
      default() {
        return {
          locations: {
            employee_regional_id: '',
            employee_headquarter_id: '',
            employee_area_id: '',
            employee_process_id: ''
          },
          date: "",
          previews_events: "",
          employee_id: "",
          exposition_level: "",
          air_left_500: "",
          air_left_1000: "",
          air_left_2000: "",
          air_left_3000: "",
          air_left_4000: "",
          air_left_6000: "",
          air_left_8000: "",
          air_right_500: "",
          air_right_1000: "",
          air_right_2000: "",
          air_right_3000: "",
          air_right_4000: "",
          air_right_6000: "",
          air_right_8000: "",
          osseous_right_500: "",
          osseous_right_1000: "",
          osseous_right_2000: "",
          osseous_right_3000: "",
          osseous_right_4000: "",
          osseous_left_500: "",
          osseous_left_1000: "",
          osseous_left_2000: "",
          osseous_left_3000: "",
          osseous_left_4000: "",
          recommendations: "",
          observation: "",
          epp: "",
          base_type: "",
          base: "",
          base_state: ""
        };
      }
    }
  },
  watch: {
    audiometry() {
      this.loading = false;
      this.form = Form.makeFrom(this.audiometry, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.audiometry, this.method),

      disabledDates: {
        from: new Date()
      }
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "biologicalmonitoring-audiometry" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
