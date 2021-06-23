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
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.econommic" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="econommic" label="Económico" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.econommic`)"></vue-advanced-select>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.quality_care_patient_safety" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="quality_care_patient_safety" label="Calidad en la atención y seguridad del paciente" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.quality_care_patient_safety`)"></vue-advanced-select>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.reputational" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="reputational" label="Reputacional" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.reputational`)"></vue-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.legal_regulatory" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="legal_regulatory" label="Legal Regulatorio" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.legal_regulatory`)"></vue-advanced-select>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.environmental" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="environmental" label="Ambiental" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.environmental`)"></vue-advanced-select>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.max_inherent_impact" label="Max. Impacto Inherente" type="number" name="max_inherent_impact"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.decription_inherent_impact" label="Desripión Impacto Inherente" type="number" name="description_inherent_impact"></vue-input>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.max_inherent_frequency" :multiple="false" :options="valuesNumericsFrecuency" :hide-selected="false" name="max_inherent_frequency" label="Max Frecuencia Inherente" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.max_inherent_frequency`)"></vue-advanced-select>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.description_inherent_frequency" label="Desripión Frecuencia Inherente" type="number" name="description_inherent_frequency"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.inherent_exposition" label="Exposición Inherente" type="number" name="inherent_exposition"></vue-input>
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
                                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="control.controls" name="controls" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.causes_controls.${indexC}.controls.${indexControl}.controls`)" label="Controles" placeholder="Seleccione los controles" :url="tagsRiskCausesControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
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

            <tab-content title="Evaluación Residual">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
               <b-form-row>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.controls_decrease" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="controls_decrease" label="Los controles apuntan a disminuir" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.controls_decrease`)"></vue-advanced-select>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.nature" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="nature" label="Naturaleza" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.nature`)"></vue-advanced-select>
                  <vue-radio :disabled="viewOnly" :checked="risk.evidency" class="col-md-4" v-model="risk.evidence" :options="siNo" name="evidence" :error="form.errorsFor('evidence')" label="Evidencia"></vue-radio>
                </b-form-row>
                <b-form-row>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.coverage" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="coverage" label="Cobertura" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.coverage`)"></vue-advanced-select>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-4" v-model="risk.documentation" :multiple="false" :options="valuesNumerics" :hide-selected="false" name="documentation" label="Documentación" placeholder="Seleccione" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.documentation`)"></vue-advanced-select>
                  <vue-radio :disabled="viewOnly" :checked="risk.evidency" class="col-md-4" v-model="risk.segregation" :options="siNo" name="segregation" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.segregation`)" label="Segregación"></vue-radio>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.control_evaluation" label="Evaluación del control" type="number" name="control_evaluation" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.control_evaluation`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.percentege_mitigation" label="% Mitigación" type="number" name="percentege_mitigation" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.percentege_mitigation`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.max_residual_impact" label="Max Impacto Residual" type="number" name="max_residual_impact" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.max_residual_impact`)"></vue-input>
                </b-form-row>
                 <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.description_residual_impact" label="Desripión Impacto Residual" type="number" name="description_residual_impact" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.description_residual_impact`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.max_residual_frequency" label="Max Frecuencia Residual" type="number" name="max_residual_frequency" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.max_residual_frequency`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.description_residual_frequency" label="Desripión Frecuencia Residual" type="number" name="description_residual_frequency" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.description_residual_frequency`)"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.residual_exposition" label="Exposición Residual" type="number" name="residual_exposition" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.residual_exposition`)"></vue-input>
                  <vue-input class="col-md-4" :disabled="true" v-model="risk.max_impact_event_risk" label="Max Impacto Evento Riesgo" type="number" name="max_impact_event_risk" :error="form.errorsFor(`subprocess.${indexSubprocess}.risk.${indexRisk}.max_impact_event_risk`)"></vue-input>
                </b-form-row>
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
    valuesNumerics: {
			type: Array,
			default: function() {
				return [
					{ name:0, value:0},
					{ name:1, value:1},
					{ name:2, value:2},
					{ name:3, value:3},
					{ name:4, value:4},
					{ name:5, value:5}
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
          max_impact_event_risk: ''
        };
      }
    }
  },
  watch: {
    risk: {
       handler(val) {
        this.loading = false;
        this.$emit('input', this.risk);
        this.updateData()
      },
      deep: true
    },
    'risk.risk_id': function() {
      this.updateDetails(`/industrialSecurity/risk/${this.risk.risk_id}`, 'riskDetail');
    }
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
      cause.controls.splice(index, 1)
    },
    updateData() {
      if (this.risk.econommic != undefined && this.risk.quality_care_patient_safety != undefined && this.risk.reputational != undefined && this.risk.legal_regulatory != undefined && this.risk.environmental != undefined)
      {
        if (this.risk.econommic && this.risk.quality_care_patient_safety && this.risk.reputational && this.risk.legal_regulatory && this.risk.environmental)
        {
          this.risk.max_inherent_impact = Math.max(this.risk.econommic, this.risk.quality_care_patient_safety, this.risk.reputational, this.risk.econommic, this.risk.environmental)
        }
      } 
      else
        this.risk.max_inherent_impact.value = ''
    },
  }
};
</script>
