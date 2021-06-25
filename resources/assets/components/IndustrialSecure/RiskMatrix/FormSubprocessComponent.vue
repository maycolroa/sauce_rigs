<template>
    <b-form autocomplete="off">
      <b-form-row>
        <vue-ajax-advanced-select @selectedName="emitSubprocessName" :disabled="viewOnly" class="col-md-6" v-model="subprocess.subprocess_id" :selected-object="subprocess.multiselect_subprocess" name="subprocess_id" :error="form.errorsFor(`subprocesses.${indexSubprocess}.subprocess_id`)" label="Subproceso" placeholder="Seleccione el subproceso" :url="subprocessesDataUrl">
          </vue-ajax-advanced-select>
      </b-form-row>
      <b-form-row>
        <div class="col-md-12" v-if="!viewOnly">
          <div class="float-right" style="padding-top: 10px;">
            <b-btn variant="primary" @click.prevent="addDanger()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Riesgo</b-btn>
          </div>
        </div>
      </b-form-row>
      <b-form-row v-if="subprocess.risks.length > 0">
        <vue-advanced-select class="col-md-12" v-model="search" :multiple="false" :options="searchOptions" :hide-selected="false" name="search" label="Buscar Riesgo" placeholder="Seleccione el riesgo">
                      </vue-advanced-select>
      </b-form-row>
      <b-form-row style="padding-top: 15px;">
          <template v-for="(risk, index) in subprocess.risks">
            <b-card no-body class="mb-2 border-secondary" :key="risk.key" style="width: 100%;" v-show="showDander(risk.risk.name)">
              <b-card-header class="bg-secondary">
                <b-row>
                  <b-col cols="10" class="d-flex justify-content-between"> {{ risk.risk.name ? risk.risk.name : `Nuevo Riesgo ${index + 1}` }}</b-col>
                  <b-col cols="2">
                    <div class="float-right">
                      <b-button-group>
                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + risk.key + '-1'" variant="link">
                          <span class="collapse-icon"></span>
                        </b-btn>
                        <b-btn @click.prevent="removeDanger(index)" 
                          v-if="subprocess.risks.length > 1 && !viewOnly"
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
              <b-collapse :id="`accordion${risk.key}-1`" :visible="!isEdit && !viewOnly" :accordion="`accordion-${indexSubprocess}`">
                <b-card-body>
                  <form-risk-component
                    :is-edit="isEdit"
                    :view-only="viewOnly"
                    :risk="risk"
                    v-model="subprocess.risks[index]"
                    :form="form"
                    :index-subprocess="indexSubprocess"
                    :index-risk="index"
                    @riskName="updateDangerNamePanel"
                    :siNo="siNo"
                    :action-plan-states="actionPlanStates"
                    :evaluation-controls="evaluationControls"
                    :impacts-description="impactsDescription"
                    :controls-decrease="controlsDecrease"
                    :nature="nature"
                    :coverage="coverage"
                    :documentation="documentation"
                    :mitigation="mitigation"
                  />
                </b-card-body>
              </b-collapse>
            </b-card>
          </template>
      </b-form-row>
    </b-form>
</template>

<script>
import FormRiskComponent from '@/components/IndustrialSecure/RiskMatrix/FormRiskComponent.vue';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import VueInput from "@/components/Inputs/VueInput.vue";

export default {
  components: {
    FormRiskComponent,
    VueRadio,
    VueAjaxAdvancedSelect,
    VueAdvancedSelect,
    PerfectScrollbar,
    VueInput
  },
  props: {
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    form: { type: Object, required: true },
    indexSubprocess: { type: Number, required: true },
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
    subprocess: {
      default() {
        return {
            key: new Date().getTime(),
            id: '',
            subprocess_id: '',
            risks: [
              {
                key: new Date().getTime(),
                id: '',
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
                causes_controls: [],
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
              }
            ],
            risksRemoved: [],
            subprocess: {
              name: ''
            }
        };
      }
    }
  },
  watch: {
    subprocess() {
      this.loading = false;
      this.$emit('input', this.subprocess);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      subprocessesDataUrl: '/selects/rmSubprocess',
      search: ''
    };
  },
  computed: {
    searchOptions() {

      let options = this.subprocess.risks
      .map((f) => {
        if (f.risk.name)
          return f.risk.name
      })
      .filter((value, index, self) => value && self.indexOf(value) === index)
      .map((f) => {
        return {"name": f, "value": f}
      })

      if (options.length == 0)
        return []

      return options;
    }
  },
  methods: {
    addDanger() {
      this.subprocess.risks.push({
        key: new Date().getTime(),
        id: '',
        risk_id: '',
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
        causes_controls: [],
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
      })
    },
    removeDanger(index) {
      if(this.subprocess.risks[index].id != '')
        this.subprocess.risksRemoved.push(this.subprocess.risks[index])
      this.subprocess.risks.splice(index, 1)
    },
    emitSubprocessName(value) {
      this.$emit('subprocessName', value, this.indexSubprocess)
    },
    updateDangerNamePanel(values, index) {
      this.subprocess.risks[index].risk.name = values
    },
    showDander(risk) {
      if (this.search != '')
      {
        return risk.toLowerCase().indexOf(this.search.toLowerCase()) !== -1 ? true : false
      }
      else
        return true;
    }
  }
};
</script>
