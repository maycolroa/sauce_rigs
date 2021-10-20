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
                  <vue-input :disabled="true" class="col-md-4" v-model="riskDetail.category" label="Categoría" type="text" name="category"></vue-input>
                  <vue-input v-if="isEdit || viewOnly" :disabled="true" class="col-md-2" v-model="risk.risk_sequence" label="# Riesgo" type="number" name="risk_sequence"></vue-input>
                  <vue-input :disabled="true" class="col-md-6" v-model="risk.nomenclature" label="Nomenclatura" type="text" name="nomenclature"></vue-input>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Evaluación Inherente">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <div class="col-md-3" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.economico" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-advanced-select :disabled="viewOnly" v-model="risk.economic" :multiple="false" :searchable="true" :options="valuesNumerics" :hide-selected="false" name="economic" label="Económico" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.economic`)"></vue-advanced-select>
                  </div>
                  <div class="col-md-6">
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.calidad" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-advanced-select :disabled="viewOnly" v-model="risk.quality_care_patient_safety" :searchable="true" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="quality_care_patient_safety" label="Calidad en la atención y seguridad del paciente" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.quality_care_patient_safety`)"></vue-advanced-select>
                  </div>
                  <div class="col-md-3" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.reputacional" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-advanced-select :disabled="viewOnly" v-model="risk.reputational" :multiple="false" :options="valuesNumerics" :searchable="true" :hide-selected="false" name="reputational" label="Reputacional" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.reputational`)"></vue-advanced-select>
                  </div>
                </b-form-row>
                <b-form-row>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.legal" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-advanced-select :disabled="viewOnly" v-model="risk.legal_regulatory" :multiple="false" :searchable="true" :options="valuesNumerics" :hide-selected="false" name="legal_regulatory" label="Legal Regulatorio" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.legal_regulatory`)"></vue-advanced-select>
                  </div>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.ambiental" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-advanced-select :disabled="viewOnly" v-model="risk.environmental" :multiple="false" :searchable="true" :options="valuesNumerics" :hide-selected="false" name="environmental" label="Ambiental" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.environmental`)"></vue-advanced-select>
                  </div>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.max_inherent_impact" label="Max. Impacto Inherente" type="number" name="max_inherent_impact" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.max_inherent_impact`)"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.description_inherent_impact" label="Descripción Impacto Inherente" type="text" name="description_inherent_impact" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.description_inherent_impact`)"></vue-input>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.max_frequence" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-advanced-select :disabled="viewOnly" v-model="risk.max_inherent_frequency" :multiple="false" :searchable="true" :options="valuesNumericsFrecuency" :hide-selected="false" name="max_inherent_frequency" label="Max. Frecuencia Inherente" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.max_inherent_frequency`)"></vue-advanced-select>
                  </div>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.description_inherent_frequency" label="Descripicón Frecuencia Inherente" type="text" name="description_inherent_frequency" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.description_inherent_frequency`)"></vue-input>
                </b-form-row>
                <b-form-row>
                  <b-col cols="2" style="padding-right: 0px;padding-left: 0px;"><label class="form-label mb-12">Exposición Inherente</label></b-col>
                  <b-col cols="10"><b-alert show class="col-md-4" :class="backgroundInputInherente">
                   <b>{{risk.inherent_exposition}}</b>
                  </b-alert></b-col>
                  <!--<vue-input class="col-md-4" :disabled="true" v-model="risk.inherent_exposition" label="Exposición Inherente" type="number" name="inherent_exposition" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.inherent_exposition`)"></vue-input>-->
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Causas y Controles">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <div class="col-md-12" v-if="!viewOnly">
                    <div class="float-right" style="padding-top: 10px;">
                      <b-btn variant="primary" @click.prevent="addCause()" v-if="!viewOnly"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa</b-btn>
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
                            <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="cause.cause" label="Causa" name="cause" placeholder="Causa" rows="3" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.causes_controls.${indexC}.cause`)"/>
                          </b-form-row>
                          <b-form-row style="padding-bottom: 20px;">
                            <div class="col-md-12">
                                <center><b-btn variant="primary" @click.prevent="addControl(indexC)" v-if="!viewOnly"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Control</b-btn></center>
                            </div>
                          </b-form-row>  

                          <template v-for="(control, indexControl) in cause.controls">
                            <div :key="control.key">
                                <b-form-row>
                                  <div class="col-md-12">
                                      <div class="float-right">
                                          <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" v-if="!viewOnly" @click.prevent="removeControl(cause, indexControl)"><span class="ion ion-md-close-circle"></span></b-btn>
                                      </div>
                                  </div>
                                  <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="control.controls" label="Control" name="controls" placeholder="Control" rows="3" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.causes_controls.${indexC}.controls.${indexControl}.controls`)"/>
                                  <!--<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="control.controls" name="controls" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.causes_controls.${indexC}.controls.${indexControl}.controls`)" label="Controles" placeholder="Seleccione los controles" :url="tagsRiskCausesControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                                  </vue-ajax-advanced-select>-->
                                  <vue-input v-if="isEdit || viewOnly"  :disabled="true" class="col-md-2" v-model="control.number_control" label="# Control" type="number" name="number_control"></vue-input>
                                  <vue-input :disabled="true" class="col-md-4" v-model="control.nomenclature" label="Nomenclatura" type="text" name="nomenclature"></vue-input>
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

            <tab-content title="Evaluación Residual">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
               <b-form-row>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.controls_decrease" :multiple="false" :options="controlsDecrease" :hide-selected="false" name="controls_decrease" label="Los controles apuntan a disminuir" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.controls_decrease`)"></vue-advanced-select>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.naturaleza" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                  <vue-advanced-select :disabled="viewOnly" v-model="risk.nature" :multiple="false" :options="nature" :hide-selected="false" name="nature" label="Naturaleza" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.nature`)"></vue-advanced-select>
                  </div>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.evidencia" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                  <vue-radio :disabled="viewOnly" :checked="risk.evidence" v-model="risk.evidence" :options="siNo" name="evidence" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.evidence`)" label="Evidencia"></vue-radio>
                  </div>
                </b-form-row>
                <b-form-row>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.cobertura" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                  <vue-advanced-select :disabled="viewOnly" v-model="risk.coverage" :multiple="false" :options="coverage" :hide-selected="false" name="coverage" label="Cobertura" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.coverage`)"></vue-advanced-select>
                  </div>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.documentacion" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-advanced-select :disabled="viewOnly" v-model="risk.documentation" :multiple="false" :options="documentation" :hide-selected="false" name="documentation" label="Documentación" placeholder="Seleccione" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.documentation`)"></vue-advanced-select>
                  </div>
                  <div class="col-md-4" >
                    <b-col>
                      <div class="float-right" style="padding-right: 10px;">
                          <b-btn v-b-popover.hover.focus.left="textHelp.segregacion" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
                      </div>
                    </b-col>
                    <vue-radio :disabled="viewOnly" :checked="risk.segregation" v-model="risk.segregation" :options="siNo" name="segregation" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.segregation`)" label="Segregación"></vue-radio>
                  </div>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.control_evaluation" label="Evaluación del control" type="text" name="control_evaluation" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.control_evaluation`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.percentege_mitigation" label="% Mitigación" type="text" name="percentege_mitigation" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.percentege_mitigation`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.max_residual_impact" label="Max. Impacto Residual" type="text" name="max_residual_impact" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.max_residual_impact`)"></vue-input>
                </b-form-row>
                 <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.description_residual_impact" label="Descripción Impacto Residual" type="text" name="description_residual_impact" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.description_residual_impact`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.max_residual_frequency" label="Max. Frecuencia Residual" type="number" name="max_residual_frequency" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.max_residual_frequency`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.description_residual_frequency" label="Descripción Frecuencia Residual" type="text" name="description_residual_frequency" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.description_residual_frequency`)"></vue-input>
                </b-form-row>
                <b-form-row>
                  <b-col cols="2" style="padding-right: 0px;padding-left: 0px;"><label class="form-label mb-12">Exposición Residual</label></b-col>
                  <b-col cols="10"><b-alert show class="col-md-4" :class="backgroundInputResidual">
                   <b>{{risk.residual_exposition}}</b>
                  </b-alert></b-col>
                </b-form-row>
                <b-form-row>
                  <!--<b-alert show label="Exposición Residual" class="col-md-4" :class="backgroundInputResidual">
                   <b>{{risk.residual_exposition}}</b>
                  </b-alert>
                  <vue-input class="col-md-4" :class="backgroundInputResidual" v-model="risk.residual_exposition" label="Exposición Residual" type="number" name="residual_exposition" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.residual_exposition`)"> </vue-input>-->
                  <vue-input class="col-md-10" :disabled="true" v-model="risk.max_impact_event_risk" label="Max Impacto Evento Riesgo" type="text" name="max_impact_event_risk" :error="form.errorsFor(`subprocesses.${indexSubprocess}.risks.${indexRisk}.max_impact_event_risk`)"></vue-input>
                </b-form-row>
                <b-form-row style="padding-bottom: 20px;">
                  <div class="col-md-12">
                      <center><b-btn variant="primary" v-if="!viewOnly" @click.prevent="addIndicator()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Indicador</b-btn></center>
                  </div>
                </b-form-row>

                <template v-for="(indicator, indexInd) in risk.indicators">
                  <div :key="indicator.key">
                      <b-form-row>
                        <div class="col-md-12">
                            <div class="float-right">
                                <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeIndicator(indexInd)"><span class="ion ion-md-close-circle"></span></b-btn>
                            </div>
                        </div>
                        <vue-input :disabled="viewOnly" class="col-md-12" v-model="indicator.indicator" label="Indicador" type="text" name="indicator"></vue-input>
                      </b-form-row>
                  </div>
                </template>
              </b-card>
            </tab-content>

            <tab-content title="Plan de acción">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <action-plan-component
                  :is-edit="isEdit"
                  :view-only="viewOnly"
                  :form="form"
                  :prefix-index="`subproess.${indexSubprocess}.risks.${indexRisk}.`"
                  :action-plan-states="actionPlanStates"
                  v-model="risk.actionPlan"
                  :action-plan="risk.actionPlan"/>
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

    if (this.risk.risk_id)
    {
      this.updateDetails(`/industrialSecurity/risk/${this.risk.risk_id}`, 'riskDetail');
    }
    
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
    nomenclature: { type: String, required: true },
    eventRisk: false,
    valuesNumerics: {
			type: Array,
			default: function() {
				return [
					{ name:0, value:'0'},
					{ name:1, value:'1'},
					{ name:2, value:'2'},
					{ name:3, value:'3'},
					{ name:4, value:'4'},
					{ name:5, value:'5'}
				];
			}
		},
    valuesNumericsFrecuency: {
			type: Array,
			default: function() {
				return [
					{ name:1, value:1},
					{ name:2, value:2},
					{ name:3, value:3},
					{ name:4, value:4},
					{ name:5, value:5}
				];
			}
		},
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
    evaluationControls: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    impactsDescription: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    controlsDecrease: {
      type: Array,
      default: function() {
        return [];
      }
    },
    nature: {
      type: Array,
      default: function() {
        return [];
      }
    },
    coverage: {
      type: Array,
      default: function() {
        return [];
      }
    },
    documentation: {
      type: Array,
      default: function() {
        return [];
      }
    },
    mitigation: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    textHelp: {
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
          economic:'',
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
              controls: [],
              indicators: []
          },
          controls_decrease: '',
          nature: '',
          evidence: '',
          coverage: '',
          documentation: '',
          segregation: '',
          control_evaluation: '',
          percentege_mitigation: '',
          max_residual_impact: '',
          description_residual_impact: '',
          max_residual_frequency: '',
          description_residual_frequency: '',
          residual_exposition: '',
          max_impact_event_risk: '',
          indicators: [
            {
              key: new Date().getTime() + 1000,
              indicator: '',
              id: ''
            }
          ],
          actionPlan: {
              activities: [],
              activitiesRemoved: []
          },
          nomenclature: ''
        };
      }
    }
  },
  watch: {
    risk: {
       handler(val) {
        this.loading = false;
        //this.$emit('input', this.risk);
        this.updateData()
      },
      deep: true
    },
    'risk.risk_id': function() {
      this.updateDetails(`/industrialSecurity/risk/${this.risk.risk_id}`, 'riskDetail');
    },
    'risk.residual_exposition': function() {
      this.updateEventRisk();
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
  computed: {
    backgroundInputResidual()
    {
      if (
      (this.risk.description_residual_frequency == 'Bajo' && this.risk.description_residual_impact == 'Extremo') || 
      (this.risk.description_residual_frequency == 'Moderada' && this.risk.description_residual_impact == 'Grave') || 
      (this.risk.description_residual_frequency == 'Alta' && this.risk.description_residual_impact == 'Moderado') ||
      (this.risk.description_residual_frequency == 'Muy Alta' && this.risk.description_residual_impact == 'Leve'))
      {
        return 'colorOrange';
      }
      else if (
      (this.risk.description_residual_frequency == 'Muy Bajo' && this.risk.description_residual_impact == 'Moderado') || 
      (this.risk.description_residual_frequency == 'Muy Bajo' && this.risk.description_residual_impact == 'Leve') || 
      (this.risk.description_residual_frequency == 'Bajo' && this.risk.description_residual_impact == 'Leve') ||
      (this.risk.description_residual_frequency == 'Bajo' && this.risk.description_residual_impact == 'No Significativo') ||
      (this.risk.description_residual_frequency == 'Moderada' && this.risk.description_residual_impact == 'No Significativo') ||
      (this.risk.description_residual_frequency == 'Muy Bajo' && this.risk.description_residual_impact == 'No Significativo'))
      {
        return 'colorGreen';
      }
      else if (
      (this.risk.description_residual_frequency == 'Moderada' && this.risk.description_residual_impact == 'Extremo') || 
      (this.risk.description_residual_frequency == 'Alta' && this.risk.description_residual_impact == 'Extremo') || 
      (this.risk.description_residual_frequency == 'Alta' && this.risk.description_residual_impact == 'Grave') ||
      (this.risk.description_residual_frequency == 'Muy Alta' && this.risk.description_residual_impact == 'Extremo') ||
      (this.risk.description_residual_frequency == 'Muy Alta' && this.risk.description_residual_impact == 'Grave') ||
      (this.risk.description_residual_frequency == 'Muy Alta' && this.risk.description_residual_impact == 'Moderado'))
      {
        return 'colorRed';
      }
      else if (
      (this.risk.description_residual_frequency == 'Muy Bajo' && this.risk.description_residual_impact == 'Extremo') || 
      (this.risk.description_residual_frequency == 'Muy Bajo' && this.risk.description_residual_impact == 'Grave') || 
      (this.risk.description_residual_frequency == 'Bajo' && this.risk.description_residual_impact == 'Grave') ||
      (this.risk.description_residual_frequency == 'Bajo' && this.risk.description_residual_impact == 'Moderado') ||
      (this.risk.description_residual_frequency == 'Moderada' && this.risk.description_residual_impact == 'Moderado') ||
      (this.risk.description_residual_frequency == 'Moderada' && this.risk.description_residual_impact == 'Leve') ||
      (this.risk.description_residual_frequency == 'Alta' && this.risk.description_residual_impact == 'Leve') ||
      (this.risk.description_residual_frequency == 'Alta' && this.risk.description_residual_impact == 'No Significativo') ||
      (this.risk.description_residual_frequency == 'Muy Alta' && this.risk.description_residual_impact == 'No Significativo'))
      {
        return 'colorYelow';
      }
      else {
        return '';
      }
    },
    backgroundInputInherente()
    {
      if (
      (this.risk.description_inherent_frequency == 'Bajo' && this.risk.description_inherent_impact == 'Extremo') || 
      (this.risk.description_inherent_frequency == 'Moderada' && this.risk.description_inherent_impact == 'Grave') || 
      (this.risk.description_inherent_frequency == 'Alta' && this.risk.description_inherent_impact == 'Moderado') ||
      (this.risk.description_inherent_frequency == 'Muy Alta' && this.risk.description_inherent_impact == 'Leve'))
      {
        return 'colorOrange';
      }
      else if (
      (this.risk.description_inherent_frequency == 'Muy Bajo' && this.risk.description_inherent_impact == 'Moderado') || 
      (this.risk.description_inherent_frequency == 'Muy Bajo' && this.risk.description_inherent_impact == 'Leve') || 
      (this.risk.description_inherent_frequency == 'Bajo' && this.risk.description_inherent_impact == 'Leve') ||
      (this.risk.description_inherent_frequency == 'Bajo' && this.risk.description_inherent_impact == 'No Significativo') ||
      (this.risk.description_inherent_frequency == 'Moderada' && this.risk.description_inherent_impact == 'No Significativo') ||
      (this.risk.description_inherent_frequency == 'Bajo' && this.risk.description_inherent_impact == 'No Significativo'))
      {
        return 'colorGreen';
      }
      else if (
      (this.risk.description_inherent_frequency == 'Moderada' && this.risk.description_inherent_impact == 'Extremo') || 
      (this.risk.description_inherent_frequency == 'Alta' && this.risk.description_inherent_impact == 'Extremo') || 
      (this.risk.description_inherent_frequency == 'Alta' && this.risk.description_inherent_impact == 'Grave') ||
      (this.risk.description_inherent_frequency == 'Muy Alta' && this.risk.description_inherent_impact == 'Extremo') ||
      (this.risk.description_inherent_frequency == 'Muy Alta' && this.risk.description_inherent_impact == 'Grave') ||
      (this.risk.description_inherent_frequency == 'Muy Alta' && this.risk.description_inherent_impact == 'Moderado'))
      {
        return 'colorRed';
      }
      else if (
      (this.risk.description_inherent_frequency == 'Muy Bajo' && this.risk.description_inherent_impact == 'Extremo') || 
      (this.risk.description_inherent_frequency == 'Muy Bajo' && this.risk.description_inherent_impact == 'Grave') || 
      (this.risk.description_inherent_frequency == 'Bajo' && this.risk.description_inherent_impact == 'Grave') ||
      (this.risk.description_inherent_frequency == 'Bajo' && this.risk.description_inherent_impact == 'Moderado') ||
      (this.risk.description_inherent_frequency == 'Moderada' && this.risk.description_inherent_impact == 'Moderado') ||
      (this.risk.description_inherent_frequency == 'Moderada' && this.risk.description_inherent_impact == 'Leve') ||
      (this.risk.description_inherent_frequency == 'Alta' && this.risk.description_inherent_impact == 'Leve') ||
      (this.risk.description_inherent_frequency == 'Alta' && this.risk.description_inherent_impact == 'No Significativo') ||
      (this.risk.description_inherent_frequency == 'Muy Alta' && this.risk.description_inherent_impact == 'No Significativo'))
      {
        return 'colorYelow';
      }
      else {
        return '';
      }
    }
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
        number_control: this.risk.causes_controls[cause].controls.length + 1,
        nomenclature: this.nomenclature + 'C.'
      })
    },
    removeControl(cause,index) {   
      if(cause.controls[index].id != '')
        this.risk.delete.controls.push(cause.controls[index].id)
      cause.controls.splice(index, 1)
    },
    addIndicator() {
      this.risk.indicators.push({
        key: new Date().getTime() + 1000,
        indicator: '',
        id: ''
      })
    },
    removeIndicator(index) {   
      if(this.risk.indicators[index].id != '')
        this.risk.delete.indicators.push(this.risk.indicators[index].id)
      this.risk.indicators.splice(index, 1)
    },
    updateData() {
      // Calculo de max impacto inherente
      if (this.risk.economic != undefined && this.risk.quality_care_patient_safety != undefined && this.risk.reputational != undefined && this.risk.legal_regulatory != undefined && this.risk.environmental != undefined)
      {
        if (this.risk.economic !== '' && this.risk.quality_care_patient_safety !== '' && this.risk.reputational !== '' && this.risk.legal_regulatory !== '' && this.risk.environmental !== '')
        {
            this.risk.max_inherent_impact = Math.max(this.risk.economic, this.risk.quality_care_patient_safety, this.risk.reputational, this.risk.legal_regulatory, this.risk.environmental)
        }
      } 
      else
        this.risk.max_inherent_impact = ''

      // Calculo de descripcion max impacto inherente
      if (this.risk.max_inherent_impact != undefined)
      {
        if (this.risk.max_inherent_impact && this.risk.max_inherent_impact > 0)
        {
          this.risk.description_inherent_impact = this.impactsDescription['impact'][this.risk.max_inherent_impact]
        }
      }
      else
        this.risk.description_inherent_impact = ''

      // calculo descripcion max frecuencia inherente
      if (this.risk.max_inherent_frequency != undefined)
      {
        if (this.risk.max_inherent_frequency && this.risk.max_inherent_frequency > 0)
        {
          this.risk.description_inherent_frequency = this.impactsDescription['frecuency'][this.risk.max_inherent_frequency]
        }
      }
      else
        this.risk.description_inherent_frequency = ''

      //calculo de exposicion inherente
      if (this.risk.max_inherent_frequency != undefined && this.risk.max_inherent_impact != undefined)
      {
        if (this.risk.max_inherent_frequency && this.risk.max_inherent_impact)
        {
          if (this.risk.max_inherent_frequency > 0 && this.risk.max_inherent_impact > 0)
          {
            this.risk.inherent_exposition = this.risk.max_inherent_frequency * this.risk.max_inherent_impact;
          }
        }
      }
      else
        this.risk.inherent_exposition = ''

      // Calculo Evaluacion control (Evaluacion residual)
      if (this.risk.nature != undefined && this.risk.evidence != undefined && this.risk.coverage != undefined && this.risk.documentation != undefined && this.risk.segregation != undefined)
      {
        if (this.risk.nature !== '' && this.risk.evidence !== '' && this.risk.coverage !== '' && this.risk.documentation !== '' && this.risk.segregation !== '')
        {
            this.risk.control_evaluation = this.evaluationControls[this.risk.nature][this.risk.evidence][this.risk.coverage][this.risk.documentation][this.risk.segregation]
        }
      } 
      else
        this.risk.control_evaluation = ''

      // Calculo de % de mitigacion
      if (this.risk.control_evaluation != undefined)
      {
        if (this.risk.control_evaluation !== '')
        {
          this.risk.percentege_mitigation = this.mitigation[this.risk.control_evaluation] + '%'
        }
      }
      else
        this.risk.percentege_mitigation = ''

      //Calculo max impacto residual
      if (this.risk.controls_decrease != undefined && this.risk.percentege_mitigation != undefined && this.risk.max_inherent_impact != undefined)
      {
        if (this.risk.percentege_mitigation !== '' && this.risk.max_inherent_impact !== '' )
        {
          if (this.risk.controls_decrease == 'Frecuencia' || this.risk.controls_decrease == '')
          {
            this.risk.max_residual_impact = this.risk.max_inherent_impact
          }
          else
          {
            this.risk.max_residual_impact = Math.ceil(this.risk.max_inherent_impact - (this.risk.max_inherent_impact * (this.mitigation[this.risk.control_evaluation] / 100)))
          }
        }
      }
       // calculo descripcion max impacto residual
      if (this.risk.max_residual_impact != undefined)
      {
        if (this.risk.max_residual_impact && this.risk.max_residual_impact > 0)
        {
          this.risk.description_residual_impact = this.impactsDescription['impact'][this.risk.max_residual_impact]
        }
      }
      else
        this.risk.description_residual_impact = ''

      // calculo de frecuencia residual
      if (this.risk.controls_decrease != undefined && this.risk.percentege_mitigation != undefined && this.risk.max_inherent_frequency != undefined)
      {
        if (this.risk.percentege_mitigation !== '' && this.risk.max_inherent_frequency !== '' )
        {
          if (this.risk.controls_decrease == 'Impacto' || this.risk.controls_decrease == '')
          {
            this.risk.max_residual_frequency = this.risk.max_inherent_frequency
          }
          else
          {
            this.risk.max_residual_frequency = Math.ceil(this.risk.max_inherent_frequency - (this.risk.max_inherent_frequency * (this.mitigation[this.risk.control_evaluation] / 100)))
          }
        }
      } 

      // calculo descripcion max impacto residual
      if (this.risk.max_residual_frequency != undefined)
      {
        if (this.risk.max_residual_frequency && this.risk.max_residual_frequency > 0)
        {
          this.risk.description_residual_frequency = this.impactsDescription['frecuency'][this.risk.max_residual_frequency]
        }
      }
      else
        this.risk.description_residual_frequency = ''

      //calculo exposicion residual
      if (this.risk.max_residual_impact != undefined && this.risk.max_residual_frequency != undefined)
      {
        if (this.risk.max_residual_impact && this.risk.max_residual_frequency)
        {
          if (this.risk.max_residual_impact > 0 && this.risk.max_residual_frequency > 0)
          {
            this.risk.residual_exposition = this.risk.max_residual_frequency * this.risk.max_residual_impact;
          }
        }
      }
    },
    updateEventRisk()
    {
      if (this.risk.residual_exposition > 0)
      {
        if (this.risk.max_inherent_impact != undefined)
        {
          if (this.risk.max_inherent_impact !== '')
          {
            if (this.risk.economic == this.risk.max_inherent_impact)
            {
              this.risk.max_impact_event_risk = 'Económico '
            }
            if (this.risk.quality_care_patient_safety == this.risk.max_inherent_impact)
            {
              this.risk.max_impact_event_risk = this.risk.max_impact_event_risk + 'Calidad en la atención y seguridad del paciente '
            }
            if (this.risk.reputational == this.risk.max_inherent_impact)
            {
              this.risk.max_impact_event_risk = this.risk.max_impact_event_risk + 'Reputacional '
            }
            if (this.risk.legal_regulatory == this.risk.max_inherent_impact)
            {
              this.risk.max_impact_event_risk = this.risk.max_impact_event_risk + 'Legal Regulatorio '
            }
            if (this.risk.environmental == this.risk.max_inherent_impact)
            {
              this.risk.max_impact_event_risk = this.risk.max_impact_event_risk + ' Ambiental'
            }
          }
        }
      }
    }
  }
};
</script>

<style lang="scss">
.colorRed {
    background-color: #f0635f;
    text-align: center;
}
.colorYelow {
    background-color: #FFD950;
    text-align: center;
}
.colorOrange {
    background-color: #fba36e;
    text-align: center;
}
.colorGreen {
    background-color: #02BC77;
    text-align: center;
}
</style>