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
                  <vue-ajax-advanced-select @selectedName="emitDangerName" :disabled="viewOnly" class="col-md-6" v-model="danger.danger_id" :selected-object="danger.multiselect_danger" name="danger_id" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.danger_id`)" label="Peligro" placeholder="Seleccione el peligro" :url="dangersDataUrl">
                      </vue-ajax-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.danger_description" name="danger_description" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.danger_description`)" label="Descripción del peligro" placeholder="Seleccione la descripción del peligro" :url="tagsDangerDescriptionDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.danger_generated" :multiple="true" :options="dangerGenerated" :hide-selected="false" name="danger_generated" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.danger_generated`)" label="Peligro Generado" placeholder="Seleccione el peligro generado">
                      </vue-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.possible_consequences_danger" name="possible_consequences_danger" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.possible_consequences_danger`)" label="Posibles consecuencias del peligro" placeholder="Seleccione las posibles consecuencias del peligro" :url="tagsPossibleConsequencesDangerDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                      </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="danger.generating_source" label="Fuente generadora" name="generating_source" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.generating_source`)"  placeholder="Fuente generadora"></vue-textarea>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Expuestos">
              <b-card bg-variant="transparent" border-variant="secondary" title="Tipo y número de expuestos" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="danger.collaborators_quantity" label="Colaboradores" type="number" name="collaborators_quantity" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.collaborators_quantity`)" placeholder="0"></vue-input>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="danger.esd_quantity" label="Contratistas" type="number" name="esd_quantity" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.esd_quantity`)" placeholder="0"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="danger.visitor_quantity" label="Visitantes" type="number" name="visitor_quantity" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.visitor_quantity`)" placeholder="0"></vue-input>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="danger.student_quantity" label="Estudiantes" type="number" name="student_quantity" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.student_quantity`)" placeholder="0"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="danger.esc_quantity" label="Arrendatarios" type="number" name="esc_quantity" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.esc_quantity`)" placeholder="0"></vue-input>
                  <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="danger.observations" label="Observaciones" name="observations" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.observations`)" placeholder="Observaciones"></vue-textarea>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Controles existentes">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_engineering_controls"  name="existing_controls_engineering_controls" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.existing_controls_engineering_controls`)" label="Controles de ingenieria" placeholder="Seleccione los controles de ingenieria" :url="tagsEngineeringControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_substitution"  name="existing_controls_substitution" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.existing_controls_substitution`)" label="Sustitución" placeholder="Seleccione las sutituciones" :url="tagsSubstitutionDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_warning_signage" name="existing_controls_warning_signage" label="Señalización, Advertencia" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.existing_controls_warning_signage`)" placeholder="Seleccione las señalización, advertencia" :url="tagsWarningSignageDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_administrative_controls" name="existing_controls_administrative_controls" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.existing_controls_administrative_controls`)" label="Controles administrativos" placeholder="Seleccione los controles administrativos" :url="tagsAdministrativeControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_epp" name="existing_controls_epp" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.existing_controls_epp`)" label="EPP" placeholder="Seleccione los EPP" :url="tagsEppDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Calificaciones">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <form-qualification-component
                  :is-edit="isEdit"
                  :view-only="viewOnly"
                  :qualifications="qualifications"
                  v-model="danger.qualifications"
                  :form="form"
                  :index-activity="indexActivity"
                  :index-danger="indexDanger"
                  :qualifications-data="danger.qualificationsData"
                />
              </b-card>
            </tab-content>

            <tab-content title="Criterios de riesgo">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-radio :disabled="viewOnly" :checked="danger.legal_requirements" class="col-md-6" v-model="danger.legal_requirements" :options="siNo" name="legal_requirements" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.legal_requirements`)" label="Cumplimiento requisitos legales">
                    </vue-radio>
                  <vue-radio :disabled="viewOnly" :checked="danger.quality_policies" class="col-md-6" v-model="danger.quality_policies" :options="siNo" name="quality_policies" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.quality_policies`)" label="Alineamiento con las políticas de calidad y de SST">
                    </vue-radio>
                </b-form-row>
                <b-form-row>
                  <vue-radio :disabled="viewOnly" :checked="danger.objectives_goals" class="col-md-6" v-model="danger.objectives_goals" :options="siNo" name="objectives_goals" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.objectives_goals`)" label="Alineamiento con los objetivos y metas">
                    </vue-radio>
                </b-form-row>
                <hr class="border-light container-m--x mt-0 mb-4">
                <b-form-row>
                  <vue-radio :disabled="true" :checked="danger.legal_requirements" class="col-md-6" v-model="danger.risk_acceptability" :options="siNo" name="risk_acceptability" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.risk_acceptability`)" label="Aceptabilidad del riesgo">
                    </vue-radio>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Medidas de Intervención">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="danger.intervention_measures_elimination" label="Eliminación" name="intervention_measures_elimination" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.intervention_measures_elimination`)" placeholder="Eliminación"></vue-textarea>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.intervention_measures_substitution"  name="intervention_measures_substitution" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.intervention_measures_substitution`)" label="Sustitución" placeholder="Seleccione las sutituciones" :url="tagsSubstitutionDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.intervention_measures_engineering_controls"  name="intervention_measures_engineering_controls" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.intervention_measures_engineering_controls`)" label="Controles de ingenieria" placeholder="Seleccione los controles de ingenieria" :url="tagsEngineeringControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.intervention_measures_warning_signage"  name="intervention_measures_warning_signage" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.intervention_measures_warning_signage`)" label="Señalización, Advertencia" placeholder="Seleccione las señalización, advertencia" :url="tagsWarningSignageDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.intervention_measures_administrative_controls" name="intervention_measures_administrative_controls" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.intervention_measures_administrative_controls`)" label="Controles administrativos" placeholder="Seleccione los controles administrativos" :url="tagsAdministrativeControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.intervention_measures_epp" name="intervention_measures_epp" :error="form.errorsFor(`activities.${indexActivity}.dangers.${indexDanger}.intervention_measures_epp`)" label="EPP" placeholder="Seleccione los EPP" :url="tagsEppDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
              </b-card>
            </tab-content>

            <tab-content title="Plan de acción" v-if="configuration.show_action_plans != undefined && configuration.show_action_plans == 'SI'">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <action-plan-component
                  :is-edit="isEdit"
                  :view-only="viewOnly"
                  :form="form"
                  :prefix-index="`activities.${indexActivity}.dangers.${indexDanger}.`"
                  :action-plan-states="actionPlanStates"
                  v-model="danger.actionPlan"
                  :action-plan="danger.actionPlan"/>
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
    indexActivity: { type: Number, required: true },
    indexDanger: { type: Number, required: true },
    dangerGenerated: {
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
    qualifications: {
      type: [Array, Object],
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
    danger: {
      default() {
        return {
            key: new Date().getTime(),
            id: '',
            dm_activity_id: '',
            danger_id: '',
            danger_generated: '',
            possible_consequences_danger: '',
            generating_source: '',
            collaborators_quantity: '',
            esd_quantity: '',
            visitor_quantity: '',
            student_quantity: '',
            esc_quantity: '',
            existing_controls_engineering_controls: '',
            existing_controls_substitution: '',
            existing_controls_warning_signage: '',
            existing_controls_administrative_controls: '',
            existing_controls_epp: '',
            legal_requirements: '',
            quality_policies: '',
            objectives_goals: '',
            risk_acceptability: '',
            intervention_measures_elimination: '',
            intervention_measures_substitution: '',
            intervention_measures_engineering_controls: '',
            intervention_measures_warning_signage: '',
            intervention_measures_administrative_controls: '',
            intervention_measures_epp: '',
            qualifications: '',
            actionPlan: {
              activities: [],
              activitiesRemoved: []
            },
            danger: {
              name: ''
            }
        };
      }
    }
  },
  watch: {
    danger() {
      this.loading = false;
      this.$emit('input', this.danger);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      dangersDataUrl: '/selects/dmDangers',
      tagsPossibleConsequencesDangerDataUrl: '/selects/tagsPossibleConsequencesDanger',
      tagsEngineeringControlsDataUrl: '/selects/tagsEngineeringControls',
      tagsWarningSignageDataUrl: '/selects/tagsWarningSignage',
      tagsAdministrativeControlsDataUrl: '/selects/tagsAdministrativeControls',
      tagsEppDataUrl: '/selects/tagsEpp',
      tagsSubstitutionDataUrl: '/selects/tagsSubstitution',
      tagsDangerDescriptionDataUrl: '/selects/tagsDangerDescription'
    };
  },
  methods: {
    emitDangerName(value) {
      this.$emit('dangerName', value, this.indexDanger)
    }
  }
};
</script>
