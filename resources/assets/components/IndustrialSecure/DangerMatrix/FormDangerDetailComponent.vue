<template>
    <b-form autocomplete="off">
      <b-card bg-variant="transparent" border-variant="secondary" title="Información General" class="mb-3 box-shadow-none">
        <b-form-row>
          <location-level-component
            :is-edit="isEdit"
            :view-only="viewOnly"
            v-model="form.locations"
            :location-level="form.locations"
            :form="form"
            application="industrialSecure"
            module="dangerMatrix"/>
        </b-form-row>

        <b-form-row>
          <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" placeholder="Nombre"></vue-input>
          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.participants"  name="participants" label="Participantes" placeholder="Seleccione los participantes" url="/selects/tagsParticipants" :multiple="true" :allowEmpty="true" :taggable="true">
          </vue-ajax-advanced-select>
          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="activity.activity_id" :selected-object="activity.multiselect_activity" name="activity_id" label="Actividad" placeholder="Seleccione la actividad" :url="activitiesDataUrl"></vue-ajax-advanced-select>
          <vue-radio :disabled="viewOnly" :checked="activity.type_activity" class="col-md-4" v-model="activity.type_activity" :options="typeActivities" name="type_activity" label="Tipo de actividad"></vue-radio>
          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.danger_id" :selected-object="danger.multiselect_danger" name="danger_id" label="Peligro" placeholder="Seleccione el peligro" :url="dangersDataUrl"></vue-ajax-advanced-select>
          <vue-input :disabled="viewOnly" class="col-md-6" v-model="danger.id" label="Código de peligro" type="text" name="id" placeholder="Código de peligro"></vue-input>
          <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="danger.generating_source" label="Fuente generadora" name="generating_source"  placeholder="Fuente generadora" rows="6"></vue-textarea>
        </b-form-row>
      </b-card>
        <form-wizard ref="wizardFormDanger">
            <!-- Allow html in tab title (this template required for the proper styling) -->
            <template slot="step" slot-scope="props">
                <wizard-step :tab="props.tab" @click.native="props.navigateToTab(props.index)" @keyup.enter.native="props.navigateToTab(props.index)" :transition="props.transition" :index="props.index">
                <span slot="title" :class="{'text-danger':props.tab.validationError}" v-html="props.tab.title"></span>
                </wizard-step>
            </template>
            <tab-content title="Calificaciones">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <form-qualification-component
                  :is-edit="isEdit"
                  :view-only="viewOnly"
                  :qualifications="qualifications"
                  v-model="danger.qualifications"
                  :qualifications-data="danger.qualificationsData"
                />
              </b-card>
            </tab-content>

            <tab-content title="Controles existentes">
              <b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_engineering_controls"  name="existing_controls_engineering_controls" label="Controles de ingenieria" placeholder="Seleccione los controles de ingenieria" :url="tagsEngineeringControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_substitution"  name="existing_controls_substitution" label="Sustitución" placeholder="Seleccione las sutituciones" :url="tagsSubstitutionDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_warning_signage" name="existing_controls_warning_signage" label="Señalización, Advertencia"  placeholder="Seleccione las señalización, advertencia" :url="tagsWarningSignageDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_administrative_controls" name="existing_controls_administrative_controls" label="Controles administrativos" placeholder="Seleccione los controles administrativos" :url="tagsAdministrativeControlsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
                <b-form-row>
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="danger.existing_controls_epp" name="existing_controls_epp"  label="EPP" placeholder="Seleccione los EPP" :url="tagsEppDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
                </b-form-row>
              </b-card>
            </tab-content>
            <template slot="footer" slot-scope="props">
                <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
                <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>
            </template>
        </form-wizard>
    </b-form>
</template>

<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import FormQualificationComponent from '@/components/IndustrialSecure/DangerMatrix/FormQualificationReportDetailComponent.vue';
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelDetailComponent.vue';

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
    ActionPlanComponent,
    LocationLevelComponent
  },
  mounted() {
    this.$refs.wizardFormDanger.activateAll();
    
    setTimeout(() => {
        this.loading = false;
    }, 2000)
  },
  props: {
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: true }, 
    form: { type: Object, required: true },
    dangerGenerated: {
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
    configuration: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    activity: {
      default() {
        return {
            activity_id: '',
            type_activity: ''
        }
      }
    },
    typeActivities: {
      type: Array,
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
            observations: '',
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
      tagsDangerDescriptionDataUrl: '/selects/tagsDangerDescription',
      activitiesDataUrl: '/selects/dmActivities',
      configLocation: {},      
    };
  },
  methods: {
    setConfigLocation(value)
    {
      this.configLocation = value
    }
  }
};
</script>
