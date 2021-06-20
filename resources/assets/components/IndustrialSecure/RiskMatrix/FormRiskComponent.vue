<template>
    <b-form autocomplete="off">
        <form-wizard ref="wizardFormDanger">
            <!-- Allow html in tab title (this template required for the proper styling) -->
            <template slot="step" slot-scope="props">
                <wizard-step :tab="props.tab" @click.native="props.navigateToTab(props.index)" @keyup.enter.native="props.navigateToTab(props.index)" :transition="props.transition" :index="props.index">
                <span slot="title" :class="{'text-danger':props.tab.validationError}" v-html="props.tab.title"></span>
                </wizard-step>
            </template>
            <!-- / -->
            <tab-content title="General">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-ajax-advanced-select @selectedName="emitDangerName" :disabled="viewOnly" class="col-md-12" v-model="risk.risk_id" :selected-object="risk.multiselect_risk" name="risk_id" :error="form.errorsFor(`subProcesses.${indexSubprocess}.risks.${indexRisk}.risk_id`)" label="Riesgo" placeholder="Seleccione el Riesgo" :url="risksDataUrl"> </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-input :disabled="true" class="col-md-6" v-model="riskDetail.category" label="Categoría" type="text" name="category"></vue-input>
                  <vue-input class="col-md-6" v-model="risk.risk_sequence" label="# Riesgo" type="number" name="risk_sequence"></vue-input>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Evaluación Inherente">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-input class="col-md-4" v-model="risk.econommic" label="Económico" type="number" name="econommic"></vue-input>
                  <vue-input class="col-md-4" v-model="risk.quality_care_patient_safety" label="Calidad en la atención y seguridad del paciente" type="number" name="quality_care_patient_safety"></vue-input>
                  <vue-input class="col-md-4" v-model="risk.reputational" label="Reputacional" type="number" name="reputational"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" v-model="risk.legal_regulatory" label="Legal Regulatorio" type="number" name="legal_regulatory"></vue-input>
                  <vue-input class="col-md-4" v-model="risk.environmental" label="Ambiental" type="number" name="environmental"></vue-input>
                  <vue-input class="col-md-4" v-model="risk.max_inherent_impact" label="Max. Impacto Inherente" type="number" name="max_inherent_impact"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" v-model="risk.decription_inherent_impact" label="Desripión Impacto Inherente" type="number" name="description_inherent_impact"></vue-input>
                  <vue-input class="col-md-4" v-model="risk.max_inherent_frequency" label="Max Frecuencia Inherente" type="number" name="max_inherent_frequency"></vue-input>
                  <vue-input class="col-md-4" v-model="risk.description_inherent_frequency" label="Desripión Frecuencia Inherente" type="number" name="description_inherent_frequency"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" v-model="risk.inherent_exposition" label="Exposición Inherente" type="number" name="inherent_exposition"></vue-input>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Causas y Controles">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <div class="col-md-12" v-if="!viewOnly">
                    <div class="float-right" style="padding-top: 10px;">
                      <b-btn variant="primary" @click.prevent="addCause()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa</b-btn>
                    </div>
                  </div>
                </b-form-row>
                <b-form-row style="padding-top: 15px;">
                  <template v-for="(cause, indexC) in risk.causes_controls">
                    <b-card no-body class="mb-2 border-secondary" :key="cause.key" style="width: 100%;">
                      <b-card-header class="bg-secondary">
                        <b-row>
                          <b-col cols="10" class="d-flex justify-content-between"> {{ cause.cause ? (cause.cause.length > 200 ? `${cause.cause.substring(0, 200)}...` : cause.cause) : `Nueva Causa ${indexC + 1}` }}
                          </b-col>
                          <b-col cols="2">
                            <div class="float-right">
                              <b-button-group>
                                <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + cause.key + '-1'" variant="link">
                                  <span class="collapse-icon"></span>
                                </b-btn>
                                <b-btn @click.prevent="removeCause(indexC)" 
                                  v-if="risk.causes_controls.length > 1 && !viewOnly"
                                  size="sm" 
                                  variant="secondary icon-btn borderless"
                                  v-b-tooltip.top title="Eliminar Riesgo">
                                  <span class="ion ion-md-close-circle"></span>
                                </b-btn>
                              </b-button-group>
                            </div>
                          </b-col>
                        </b-row>
                      </b-card-header>
                      <b-collapse :id="`accordion${cause.key}-1`" :visible="!isEdit && !viewOnly" :accordion="`accordion-risk.${indexRisk}.causes_controls.${indexC}`">
                        <b-card-body>
                          <b-form-row>
                            <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="cause.cause" label="Causa" name="cause" placeholder="cause" rows="3"/>
                          </b-form-row>
                          <b-form-row style="padding-bottom: 20px;">
                            <div class="col-md-12">
                                <center><b-btn variant="primary" @click.prevent="addControl(indexC)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Control</b-btn></center>
                            </div>
                          </b-form-row>  

                          <template v-for="(control, indexControl) in cause.controls">
                            <div :key="control.key">
                                <b-form-row>
                                  <div class="col-md-12">
                                      <div class="float-right">
                                          <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeControl(cause, indexControl)"><span class="ion ion-md-close-circle"></span></b-btn>
                                      </div>
                                  </div>
                                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="control.controls" name="controls" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.controls`)" label="Controles" placeholder="Seleccione los controles" :url="tagsRiskCausesControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                                  </vue-ajax-advanced-select>
                                  <vue-input class="col-md-2" v-model="control.number_control" label="# Control" type="number" name="number_control"></vue-input>
                                  <vue-input class="col-md-4" v-model="control.nomenclature" label="Nomenclatura" type="text" name="nomenclature"></vue-input>
                               </b-form-row>
                            </div>
                          </template>
                        </b-card-body>
                      </b-collapse>
                    </b-card>
                  </template>
                </b-form-row>
              </b-card>
            </tab-content>

            <template slot="footer" slot-scope="props">
                <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
                <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>
            </template>
        </form-wizard>
      <b-form-row>
      </b-form-row>
    </b-form>
</template>

<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import FormQualificationComponent from '@/components/IndustrialSecure/DangerMatrix/FormQualificationComponent.vue';
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";

export default {
  components: {
    VueRadio,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueTextarea,
    VueInput,
    FormWizard,
    TabContent,
    WizardStep,
    FormQualificationComponent,
    ActionPlanComponent
  },
  mounted() {
    this.$refs.wizardFormDanger.activateAll();
    
    setTimeout(() => {
        this.loading = false;
    }, 2000)
  },
  props: {
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false }, 
    form: { type: Object, required: true },
    indexSubprocess: { type: Number, required: true },
    indexRisk: { type: Number, required: true },
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    actionPlanStates: {
      type: Array,
      default: function() {
        return [];
      }
    },
    configuration: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    risk: {
      default() {
        return {
          key: new Date().getTime(),
          id: '',
          rm_subprocess_id: '',
          risk_id: '',
          risk_sequence: '',
          risk: {
            name: ''
          },
          econommic:'',
          quality_care_patient_safety: '',
          reputational: '',
          legal_regulatory: '',
          environmental: '',
          max_inherent_impact: '',
          description_inherent_impact: '',
          max_inherent_frequency: '',
          description_inherent_frequency: '',
          inherent_exposition: '',
          causes_controls: [
            {
              key: new Date().getTime() + 1000,
              cause: '',
              id: '',
              controls: []
            }
          ],
          delete: {
              causes: [],
              controls: []
          }
        };
      }
    }
  },
  watch: {
    risk() {
      this.loading = false;
      this.$emit('input', this.risk);
    },
    'risk.risk_id': function() {
      this.updateDetails(`/industrialSecurity/risk/${this.risk.risk_id}`, 'riskDetail');
    },
  },
  data() {
    return {
      loading: this.isEdit,
      risksDataUrl: '/selects/rmRisk',
      riskDetail: [],      
      tagsRiskCausesControlsDataUrl: '/selects/tagsRmRiskCausesControls'
    };
  },
  methods: {
    emitDangerName(value) {
      this.$emit('riskName', value, this.indexRisk)
    },
    updateDetails(url, key)
    {
      this.isLoading = true;
      axios.get(url)
      .then(response => {
          this[key] = response.data.data;
          this.isLoading = false;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    addCause() {
      this.risk.causes_controls.push({
        key: new Date().getTime() + 1000,
        cause: '',
        id: '',
        controls: []
      })
    },
    removeCause(index) {
      if(this.risk.causes_controls[index].id != '')
        this.risk.delete.causes.push(this.risk.causes_controls[index])
      this.risk.causes_controls.splice(index, 1)
    },
    addControl(cause) {
      this.risk.causes_controls[cause].controls.push({
        key: new Date().getTime(),
        id: '',
        controls: '',
        number_control: '',
        nomenlature: ''
      })
    },
    removeControl(cause,index) {   
      if(cause.controls[index].id != '')
        this.risk.delete.controls.push(cause.controls[index].id)
      this.risk.causes_controls.splice(index, 1)
    },
  }
};
</script>
