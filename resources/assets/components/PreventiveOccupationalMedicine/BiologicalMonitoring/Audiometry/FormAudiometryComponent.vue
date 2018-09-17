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

      <tab-content title="General">
        <b-form-row>
          <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date" label="Fecha" :full-month-name="true" placeholder="Seleccione la fecha" :error="form.errorsFor('date')" name="date" :disabled-dates="disabledDates">
          </vue-datepicker>

          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_id" :error="form.errorsFor('employee_id')" :selected-object="form.multiselect_employee" name="employee_id" label="Empleado" placeholder="Seleccione el empleado" :url="employeesDataUrl">
          </vue-ajax-advanced-select>
        </b-form-row>
        <vue-textarea :disabled="viewOnly" v-model="form.previews_events" label="Eventos previos" :error="form.errorsFor('previews_events')" name="previews_events" placeholder="Eventos previos"></vue-textarea>

        <b-form-row>
          <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.type" label="Tipo" type="text" name="type" :error="form.errorsFor('type')" placeholder="Tipo"></vue-input>
          <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.work_zone_noise" label="Ruido de la zona de trabajo" type="text" name="work_zone_noise" :error="form.errorsFor('work_zone_noise')" placeholder="Ruido de la zona de trabajo"></vue-input>
        </b-form-row>
        <b-form-row>
          <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.exposition_level" :error="form.errorsFor('exposition_level')" :multiple="false" :options="expositionLevel" :hide-selected="false" name="exposition_level" label="Nivel de exposicion (Disometría)" placeholder="Seleccione el nivel de exposicion (Disometría)">
          </vue-advanced-select>
          <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.epp" :error="form.errorsFor('epp')" :multiple="true" :options="epp" name="epp" label="Elementos de protección personal" placeholder="Seleccione los elementos de protección personal">
          </vue-advanced-select>
        </b-form-row>
        <vue-textarea :disabled="viewOnly" v-model="form.recommendations" label="Recomendaciones generales" :error="form.errorsFor('recommendations')" name="recommendations" placeholder="Recomendaciones generales"></vue-textarea>

        <vue-textarea :disabled="viewOnly" v-model="form.observation" label="Observación general" :error="form.errorsFor('observation')" name="observation" placeholder="Observación general"></vue-textarea>
      </tab-content>
      <tab-content title="Via aerea">
        <div class="row">

          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="primary" title="Derecha Aereo" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_500" label="500 Hz" type="number" name="right_500" :error="form.errorsFor('right_500')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_1000" label="1000 Hz" type="number" name="right_1000" :error="form.errorsFor('right_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_2000" label="2000 Hz" type="number" name="right_2000" :error="form.errorsFor('right_2000')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_3000" label="3000 Hz" type="number" name="right_3000" :error="form.errorsFor('right_3000')" placeholder="0"></vue-input>
                </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_4000" label="4000 Hz" type="number" name="right_4000" :error="form.errorsFor('right_4000')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_6000" label="6000 Hz" type="number" name="right_6000" :error="form.errorsFor('right_6000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
              <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_8000" label="8000 Hz" type="number" name="right_8000" :error="form.errorsFor('right_8000')" placeholder="0"></vue-input>
              <b-col></b-col>
              </b-form-row>
            </b-card>

          </div>
          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="secondary" title="Izquierda Aereo" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_500" label="500 Hz" type="number" name="left_500" :error="form.errorsFor('left_500')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_1000" label="1000 Hz" type="number" name="left_1000" :error="form.errorsFor('left_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_2000" label="2000 Hz" type="number" name="left_2000" :error="form.errorsFor('left_2000')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_3000" label="3000 Hz" type="number" name="left_3000" :error="form.errorsFor('left_3000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_4000" label="4000 Hz" type="number" name="left_4000" :error="form.errorsFor('left_4000')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_6000" label="6000 Hz" type="number" name="left_6000" :error="form.errorsFor('left_6000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_8000" label="8000 Hz" type="number" name="left_8000" :error="form.errorsFor('left_8000')" placeholder="0"></vue-input>
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
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_500" label="500 Hz" type="number" name="right_500" :error="form.errorsFor('right_500')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_1000" label="1000 Hz" type="number" name="right_1000" :error="form.errorsFor('right_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_2000" label="2000 Hz" type="number" name="right_2000" :error="form.errorsFor('right_2000')" placeholder="0"></vue-input>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_3000" label="3000 Hz" type="number" name="right_3000" :error="form.errorsFor('right_3000')" placeholder="0"></vue-input>
                </b-form-row>
              <b-form-row>
                <vue-input :disabled="viewOnly" class="col-md" append="dB" v-model="form.right_4000" label="4000 Hz" type="number" name="right_4000" :error="form.errorsFor('right_4000')" placeholder="0"></vue-input>
                <b-col></b-col>
              </b-form-row>
            </b-card>

          </div>
          <div class="col-lg">

            <b-card bg-variant="transparent" border-variant="secondary" title="Izquierda Oseo" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_500" label="500 Hz" type="number" name="left_500" :error="form.errorsFor('left_500')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_1000" label="1000 Hz" type="number" name="left_1000" :error="form.errorsFor('left_1000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_2000" label="2000 Hz" type="number" name="left_2000" :error="form.errorsFor('left_2000')" placeholder="0"></vue-input>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_3000" label="3000 Hz" type="number" name="left_3000" :error="form.errorsFor('left_3000')" placeholder="0"></vue-input>
              </b-form-row>
              <b-form-row>
                <vue-input append="dB" :disabled="viewOnly" class="col-md" v-model="form.left_4000" label="4000 Hz" type="number" name="left_4000" :error="form.errorsFor('left_4000')" placeholder="0"></vue-input>
                <b-col></b-col>
              </b-form-row>
            </b-card>

          </div>
        </div>
      </tab-content>
      <tab-content v-if="viewOnly" title="Resultado">
        Step Content 1
      </tab-content>

      <template slot="footer" slot-scope="props">
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>
        <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
        <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>
        <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
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
import Form from "@/utils/Form.js";
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
    WizardStep
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
          date: "",
          previews_events: "",
          type: "",
          employee_id: "",
          work_zone_noise: "",
          exposition_level: "",
          left_500: "",
          left_1000: "",
          left_2000: "",
          left_3000: "",
          left_4000: "",
          left_6000: "",
          left_8000: "",
          right_500: "",
          right_1000: "",
          right_2000: "",
          right_3000: "",
          right_4000: "",
          right_6000: "",
          right_8000: "",
          left_clasification: "",
          right_clasification: "",
          recommendations: "",
          observation: "",
          test_score: "",
          epp: ""
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
