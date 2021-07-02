<template>
  <div>
    <b-row v-if="!viewOnly">
      <b-col>
        <div class="float-right" style="padding-bottom: 20px;">
            <modals-create-component
              :conf-location="configLocation"/>
        </div>
      </b-col>
    </b-row>

    <b-form :action="url" @submit.prevent="submit" autocomplete="off">

      <b-form-row>
        <vue-radio :disabled="viewOnly" :checked="form.approved" class="col-md-6" v-model="form.approved" :options="siNo" name="approved" :error="form.errorsFor('approved')" label="¿Aprobar mátriz?">
          </vue-radio>
      </b-form-row>
      
      <b-form-row>
        <location-level-component
          :is-edit="isEdit"
          :view-only="viewOnly"
          v-model="form.locations"
          :location-level="riskMatrix.locations"
          :form="form"
          application="industrialSecure"
          module="riskMatrix"
          @configLocation="setConfigLocation"/>
      </b-form-row>

      <b-form-row>
        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>

        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.participants"  name="participants" :error="form.errorsFor(`participants`)" label="Participantes" placeholder="Seleccione los participantes" url="/selects/tagsRmParticipants" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>
      </b-form-row>

      <b-form-row>
        <div class="col-md-12" v-if="!viewOnly">
          <div class="float-right" style="padding-top: 20px;">
            <b-btn variant="primary" @click.prevent="addActiviy()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Subproceso</b-btn>
          </div>
        </div>
      </b-form-row>

      <b-form-row>
        <b-card no-body variant="white" class="mb-3" style="width: 100%;">
          <b-tabs card pills class="nav-responsive-md md-pills-light">
            <b-tab v-for="(subprocess, index) in form.subprocesses" :key="subprocess.key">
                <template slot="title">
                  <strong>{{ form.subprocesses[index].sub_process.name ? form.subprocesses[index].sub_process.name : `Nuevo Subproceso ${index + 1}` }}</strong> 
                  <b-btn @click.prevent="removeActivity(index)" 
                    v-if="form.subprocesses.length > 1 && !viewOnly"
                    size="sm" 
                    variant="outline-primary icon-btn borderless"
                    v-b-tooltip.top title="Eliminar Subproceso">
                    <span class="ion ion-md-close-circle"></span>
                  </b-btn>
                </template>
                  
                <form-subprocess-component
                  :is-edit="isEdit"
                  :view-only="viewOnly"
                  :subprocess="subprocess"
                  v-model="form.subprocesses[index]"
                  :form="form"
                  :index-subprocess="index"
                  @subprocessName="updateActivityNameTab"
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
            </b-tab>
          </b-tabs>
        </b-card>
      </b-form-row>

      <div class="row float-right pt-10 pr-10">
        <template>
          <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;

          <b-btn @click="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
        </template>
      </div>
    </b-form>
  </div>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import FormSubprocessComponent from '@/components/IndustrialSecure/RiskMatrix/FormSubprocessComponent.vue';
import ModalsCreateComponent from '@/components/IndustrialSecure/RiskMatrix/ModalsCreateComponent.vue';
import LocationLevelComponent from '@/components/IndustrialSecure/RiskMatrix/LocationLevelComponent.vue';
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
  components: {
    VueInput,
    VueRadio,
    FormSubprocessComponent,
    ModalsCreateComponent,
    LocationLevelComponent,
    VueAjaxAdvancedSelect,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
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
    riskMatrix: {
      default() {
        return {
            locations: {
              employee_regional_id: '',
              employee_headquarter_id: '',
              employee_area_id: '',
              employee_process_id: '',
              macroprocess_id: ''
            },
            name: '',
            approved: '',
            participants: '',
            subprocesses: [
              {
                key: new Date().getTime(),
                id: '',
                sub_process_id: '',
                risks: [],
                risksRemoved: [],
                sub_process: {
                  name: ''
                }
              }
            ],
            subprocessesRemoved: []
        };
      }
    }
  },
  watch: {
    riskMatrix() {
      this.form = Form.makeFrom(this.riskMatrix, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.riskMatrix, this.method),
      configLocation: {},      
    }
  },
  methods: {
    submit(redirect = true) {
      this.loading = true;
      this.form
        .submit(this.url)
        .then(response => {
          this.loading = false;

          if (redirect)
            this.$router.push({ name: "industrialsecure-riskmatrix" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addActiviy() {
      this.riskMatrix.subprocesses.push(
        {
          key: new Date().getTime(),
          id: '',
          sub_process_id: '',
          risks: [],
          risksRemoved: [],
          sub_process: {
            name: ''
          }
        }
      )
    },
    removeActivity(index) {
      if (this.riskMatrix.subprocesses[index].id != '')
        this.form.subprocessesRemoved.push(this.riskMatrix.subprocesses[index])
      this.riskMatrix.subprocesses.splice(index, 1)
    },
    updateActivityNameTab(values, index) {
      this.form.subprocesses[index].sub_process.name = values
    },
    setConfigLocation(value)
    {
      this.configLocation = value
    }
  }
};
</script>
