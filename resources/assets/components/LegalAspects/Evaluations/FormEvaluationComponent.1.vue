<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <form-wizard ref="wizardFormEvaluation">
        <!-- Allow html in tab title (this template required for the proper styling) -->
        <template slot="step" slot-scope="props">
            <wizard-step :tab="props.tab" @click.native="props.navigateToTab(props.index)" @keyup.enter.native="props.navigateToTab(props.index)" :transition="props.transition" :index="props.index">
            <span slot="title" :class="{'text-danger':props.tab.validationError}" v-html="props.tab.title"></span>
            </wizard-step>
        </template>
        <tab-content title="Contratista">
          <b-row>
                <b-col>
                    <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.information_contract_lessee_id" :selected-object="form.multiselect_information_contract_lessee_id" name="information_contract_lessee_id" label="Contratista" placeholder="Seleccione la contratista" :url="contractDataUrl" :error="form.errorsFor('information_contract_lessee_id')">
                        </vue-ajax-advanced-select>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <information-general :contractor="contractor_information"/>
                </b-col>
          </b-row>
          
        </tab-content>
        
        <!-- / -->
        <tab-content title="General">
            <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
            <vue-radio :disabled="viewOnly" :checked="form.type" class="col-md-6" v-model="form.type" :options="typesEvaluation" name="type" :error="form.errorsFor('type')" label="Tipo de evaluación">
                </vue-radio>
            </b-form-row>

            <b-form-row>
                <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.evaluators_id" :selected-object="form.multiselect_evaluators_id" name="evaluators_id" label="Evaluadores" placeholder="Seleccione los evaluadores" :url="userDataUrl" :error="form.errorsFor('evaluators_id')" :multiple="true" :allowEmpty="true">
                    </vue-ajax-advanced-select>
            </b-form-row>

            <b-form-row>
                <div class="col-md-12" v-if="!viewOnly">
                <div class="float-right" style="padding-top: 10px;">
                    <b-btn variant="primary" @click.prevent="addInterviewed()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Entrevistado</b-btn>
                </div>
                </div>
            </b-form-row>

            <b-form-row style="padding-top: 15px;">
                <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 300px; padding-right: 15px; width: 100%;">
                <b-card no-body class="mb-2 border-secondary" style="width: 100%;" v-if="form.interviewees && form.interviewees.length > 0">
                    <b-card-header class="bg-secondary">
                        <b-row>
                            <b-col cols="10" class="d-flex justify-content-between text-white"> Entrevistados </b-col>
                            <b-col cols="2">
                                <div class="float-right">
                                    <b-button-group>
                                    <b-btn href="javascript:void(0)" v-b-toggle="'accordion-1'" variant="link">
                                        <span class="collapse-icon"></span>
                                    </b-btn>
                                    </b-button-group>
                                </div>
                            </b-col>
                        </b-row>
                    </b-card-header>
                    <b-collapse id="accordion-1" visible accordion="accordion-1">
                    <b-card-body>
                        <template v-for="(item, index) in form.interviewees">
                          <div :key="index">
                            <b-form-row>
                                <div class="col-md-12" v-if="!viewOnly">
                                <div class="float-right">
                                    <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Entrevistado" @click.prevent="removeInterviewed(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                                </div>
                                </div>
                            </b-form-row>
                            <b-form-row>
                                <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.interviewees[index].name" label="Nombre" type="text" name="name" :error="form.errorsFor(`interviewees.${index}.name`)" placeholder="Nombre"></vue-input>
                                <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.interviewees[index].position" label="Cargo" type="text" name="position" :error="form.errorsFor(`interviewees.${index}.position`)" placeholder="Cargo"></vue-input>
                            </b-form-row>
                            <hr class="border-light container-m--x mt-0 mb-4">
                          </div>
                        </template>
                    </b-card-body>
                    </b-collapse>
                </b-card>
                </perfect-scrollbar>
            </b-form-row>
        </tab-content>

        <tab-content title="Objetivos">
            <div class="col-md-12">
              <blockquote class="blockquote text-center">
                  <p class="mb-0">Objetivos de la evaluación</p>
              </blockquote>
              <b-form-row>
                  <div class="col-md-12" v-if="!viewOnly">
                  <div class="float-right" style="padding-top: 10px;">
                      <b-btn variant="primary" @click.prevent="addObjetive()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Objetivo</b-btn>
                  </div>
                  </div>
              </b-form-row>
              <b-form-row style="padding-top: 15px;">
                  <b-form-feedback class="d-block" v-if="form.errorsFor(`objectives`)" style="padding-bottom: 10px;">
                      {{ form.errorsFor(`objectives`) }}
                  </b-form-feedback>
                  <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                      <template v-for="(objetive, index) in form.objectives">
                          <b-card no-body class="mb-2 border-secondary" :key="objetive.key" style="width: 100%;">
                          <b-card-header class="bg-secondary">
                              <b-row>
                                <b-col cols="10" class="d-flex justify-content-between text-white"> {{ form.objectives[index].description ? form.objectives[index].description : `Nuevo objetivo ${index + 1}` }}</b-col>
                                <b-col cols="2">
                                    <div class="float-right">
                                    <b-button-group>
                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + objetive.key+'-1'" variant="link">
                                        <span class="collapse-icon"></span>
                                        </b-btn>
                                        <b-btn @click.prevent="removeObjetive(index)" 
                                        v-if="!viewOnly"
                                        size="sm" 
                                        variant="secondary icon-btn borderless"
                                        v-b-tooltip.top title="Eliminar Objetivo">
                                        <span class="ion ion-md-close-circle"></span>
                                        </b-btn>
                                    </b-button-group>
                                    </div>
                                </b-col>
                              </b-row>
                          </b-card-header>
                          <b-collapse :id="`accordion${objetive.key}-1`" visible :accordion="`accordion-123`">
                              <b-card-body>
                                <b-form-row>
                                  <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.objectives[index].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`objectives.${index}.description`)" rows="1"></vue-textarea>
                                </b-form-row>
                                <b-form-row>
                                    <div class="col-md-12" v-if="!viewOnly">
                                    <div class="float-right" style="padding-top: 10px;">
                                        <b-btn variant="primary" @click.prevent="addSubobjective(index)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Subobjetivo</b-btn>
                                    </div>
                                    </div>
                                </b-form-row>
                                <b-form-row style="padding-top: 15px;">
                                  <b-form-feedback class="d-block" v-if="form.errorsFor(`objectives.${index}.subobjectives`)" style="padding-bottom: 10px;">
                                      {{ form.errorsFor(`objectives.${index}.subobjectives`) }}
                                  </b-form-feedback>
                                      <template v-for="(subobjective, index2) in objetive.subobjectives" style="padding-right: 15px;">
                                          <b-card no-body class="mb-2 border-secondary" :key="subobjective.key" style="width: 100%;">
                                          <b-card-header class="bg-secondary">
                                              <b-row>
                                                <b-col cols="10" class="d-flex justify-content-between text-white"> {{ form.objectives[index].subobjectives[index2].description ? form.objectives[index].subobjectives[index2].description : `Nuevo Subobjetivo ${index2 + 1}` }}</b-col>
                                                <b-col cols="2">
                                                    <div class="float-right">
                                                    <b-button-group>
                                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + subobjective.key+'-1'" variant="link">
                                                        <span class="collapse-icon"></span>
                                                        </b-btn>
                                                        <b-btn @click.prevent="removeSubobjective(index, index2)" 
                                                        v-if="!viewOnly"
                                                        size="sm" 
                                                        variant="secondary icon-btn borderless"
                                                        v-b-tooltip.top title="Eliminar Subobjetivo">
                                                        <span class="ion ion-md-close-circle"></span>
                                                        </b-btn>
                                                    </b-button-group>
                                                    </div>
                                                </b-col>
                                              </b-row>
                                          </b-card-header>
                                          <b-collapse :id="`accordion${subobjective.key}-1`" visible :accordion="`accordion-1234`">
                                              <b-card-body>
                                                <b-form-row>
                                                  <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.objectives[index].subobjectives[index2].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`objectives.${index}.subobjectives.${index2}.description`)" rows="1"></vue-textarea>
                                                </b-form-row>
                                                <b-form-row>
                                                    <div class="col-md-12" v-if="!viewOnly">
                                                    <div class="float-right" style="padding-top: 10px;">
                                                        <b-btn variant="primary" @click.prevent="addItem(index, index2)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Item</b-btn>
                                                    </div>
                                                    </div>
                                                </b-form-row>
                                                <b-form-row style="padding-top: 15px;">
                                                  <b-form-feedback class="d-block" v-if="form.errorsFor(`objectives.${index}.subobjectives.${index2}.items`)" style="padding-bottom: 10px;">
                                                      {{ form.errorsFor(`objectives.${index}.subobjectives.${index2}.items`) }}
                                                  </b-form-feedback>
                                                  <div class="table-responsive" style="padding-right: 15px;">
                                                    <table class="table table-bordered table-hover" v-if="subobjective.items.length > 0">
                                                      <thead class="bg-secondary" style="color: white;">
                                                        <tr>
                                                          <th scope="col" class="align-middle" v-if="!viewOnly">#</th>
                                                          <th scope="col" class="align-middle">Descripción</th>
                                                          <template v-for="(type, indexType) in typesRating">
                                                            <th scope="col" class="align-middle text-center" :key="indexType">{{ type.name }}</th>
                                                          </template>
                                                        </tr>
                                                      </thead>
                                                      <tbody>
                                                        <template v-for="(item, index3) in subobjective.items">
                                                          <tr :key="index3">
                                                            <td class="align-middle" v-if="!viewOnly">
                                                              <b-btn @click.prevent="removeItem(index, index2, index3)" 
                                                                size="xs" 
                                                                variant="outline-primary icon-btn borderless"
                                                                v-b-tooltip.top title="Eliminar Item">
                                                                <span class="ion ion-md-close-circle"></span>
                                                                </b-btn>
                                                            </td>
                                                            <td style="padding: 0px;">
                                                              <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.objectives[index].subobjectives[index2].items[index3].description" label="" name="description" placeholder="Descripción" :error="form.errorsFor(`objectives.${index}.subobjectives.${index2}.items.${index3}.description`)" rows="1"></vue-textarea>
                                                            </td>
                                                            <template v-for="(type, indexType) in typesRating">
                                                              <td :key="indexType" class="align-middle" style="padding: 0px;">
                                                                <center><vue-checkbox-simple :disabled="viewOnly" class="col-md-1" label="" v-model="form.objectives[index].subobjectives[index2].items[index3].ratings[type.id].apply" :checked="form.objectives[index].subobjectives[index2].items[index3].ratings[type.id].apply" name="type_role" checked-value="SI" unchecked-value="NO"></vue-checkbox-simple></center>
                                                              </td>

                                                            </template>
                                                          </tr>
                                                        </template>
                                                      </tbody>
                                                    </table>
                                                  </div>
                                                </b-form-row>
                                              </b-card-body>
                                          </b-collapse>
                                          </b-card>
                                      </template>
                              </b-form-row>
                              </b-card-body>
                          </b-collapse>
                          </b-card>
                      </template>
                  </perfect-scrollbar>
              </b-form-row>
            </div>
        </tab-content>

        <tab-content title="Evaluación" v-if="viewOnly || isEvaluation">
            <div class="col-md-12">
              <blockquote class="blockquote text-left pb-10" style="padding-bottom: 5px; padding-top:5px;">
                  <p class="mb-0"><b>Fecha de evaluación:</b> {{ evaluation.evaluation_date ? evaluation.evaluation_date : 'Sin evaluar'}}</p>
              </blockquote>
              <blockquote class="blockquote text-center">
                  <p class="mb-0">Objetivos de la evaluación</p>
              </blockquote>
              <b-form-row style="padding-top: 15px;">
                  <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                      <template v-for="(objetive, index) in form.objectives">
                          <b-card no-body class="mb-2 border-secondary" :key="objetive.key" style="width: 100%;">
                          <b-card-header class="bg-secondary">
                              <b-row>
                                <b-col cols="10" class="d-flex justify-content-between text-white"> {{ form.objectives[index].description }}</b-col>
                                <b-col cols="2">
                                    <div class="float-right">
                                    <b-button-group>
                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + objetive.key+'-12'" variant="link">
                                        <span class="collapse-icon"></span>
                                        </b-btn>
                                    </b-button-group>
                                    </div>
                                </b-col>
                              </b-row>
                          </b-card-header>
                          <b-collapse :id="`accordion${objetive.key}-12`" visible :accordion="`accordion-123543`">
                              <b-card-body>
                                <b-form-row>
                                  <vue-textarea :disabled="true" class="col-md-12" v-model="form.objectives[index].description" label="Descripción" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                                </b-form-row>
                                <b-form-row style="padding-top: 15px;">
                                      <template v-for="(subobjective, index2) in objetive.subobjectives" style="padding-right: 15px;">
                                          <b-card no-body class="mb-2 border-secondary" :key="subobjective.key" style="width: 100%;">
                                          <b-card-header class="bg-secondary">
                                              <b-row>
                                                <b-col cols="10" class="d-flex justify-content-between text-white"> {{ form.objectives[index].subobjectives[index2].description }}</b-col>
                                                <b-col cols="2">
                                                    <div class="float-right">
                                                    <b-button-group>
                                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + subobjective.key+'-12'" variant="link">
                                                        <span class="collapse-icon"></span>
                                                        </b-btn>
                                                    </b-button-group>
                                                    </div>
                                                </b-col>
                                              </b-row>
                                          </b-card-header>
                                          <b-collapse :id="`accordion${subobjective.key}-12`" visible :accordion="`accordion-123456`">
                                              <b-card-body>
                                                <b-form-row>
                                                  <vue-textarea :disabled="true" class="col-md-12" v-model="form.objectives[index].subobjectives[index2].description" label="Descripción" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                                                </b-form-row>
                                                <b-form-row style="padding-top: 15px;">
                                                  <div class="table-responsive" style="padding-right: 15px;">
                                                    <table class="table table-bordered table-hover">
                                                      <thead class="bg-secondary" style="color: white;">
                                                        <tr>
                                                          <th scope="col" class="align-middle text-center">#</th>
                                                          <th scope="col" class="align-middle">Descripción</th>
                                                          <template v-for="(type, indexType) in typesRating">
                                                            <th scope="col" class="align-middle text-center" :key="indexType">{{ type.name }}</th>
                                                          </template>
                                                        </tr>
                                                      </thead>
                                                      <tbody>
                                                        <template v-for="(item, index3) in subobjective.items">
                                                          <tr :key="index3">
                                                            <td class="align-middle">
                                                                <modal-observations v-model="form.objectives[index].subobjectives[index2].items[index3].observations" :is-evaluation="isEvaluation" @removeObservation="pushRemoveObservation" :form="form" :prefixIndex="`objectives.${index}.subobjectives.${index2}.items.${index3}.observations`"/>
                                                            </td>
                                                            <td style="padding: 0px;">
                                                              <vue-textarea :disabled="true" class="col-md-12" v-model="form.objectives[index].subobjectives[index2].items[index3].description" label="" name="description" placeholder="Descripción"  rows="1"></vue-textarea>
                                                            </td>
                                                            <template v-for="(type, indexType) in typesRating">
                                                              <td :key="indexType" class="align-middle text-nowrap" style="padding: 0px;">
                                                                <center>

                                                                <template v-if="form.objectives[index].subobjectives[index2].items[index3].ratings[type.id].apply == 'SI'">

                                                                  <vue-radio v-if="isEvaluation" :checked="form.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value" class="col-md-12" v-model="form.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value" :options="[{'text':'SI','value':'SI'},{'text':'NO','value':'NO'}]" :name="`value${item.id}${type.id}`" label="" :error="form.errorsFor(`objectives.${index}.subobjectives.${index2}.items.${index3}.ratings.${type.id}.value`)"></vue-radio>

                                                                  <template v-if="!isEvaluation">
                                                                    {{ form.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value }}
                                                                  </template>

                                                                </template>
                                                                <template v-else>
                                                                  N/A
                                                                </template>

                                                                </center>
                                                              </td>

                                                            </template>
                                                          </tr>
                                                        </template>
                                                        <tr v-if="!isEvaluation && evaluation.evaluation_date">
                                                          <td colspan="2">Porcentaje de cumplimiento</td>
                                                          <td v-for="(report, indexR) in subobjective.report" :key="indexR" class="bg-secondary text-white align-middle text-center">
                                                            {{report.percentage}}%
                                                          </td>
                                                        </tr>
                                                      </tbody>
                                                    </table>
                                                  </div>
                                                </b-form-row>
                                              </b-card-body>
                                          </b-collapse>
                                          </b-card>
                                      </template>
                              </b-form-row>
                              </b-card-body>
                          </b-collapse>
                          </b-card>
                      </template>
                  </perfect-scrollbar>
              </b-form-row>
            </div>
        </tab-content>

        <template slot="footer" slot-scope="props">
            <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? (isEvaluation ? "Cancelar" : "Atras") : "Cancelar"}}</b-btn>
            <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
            <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>
            <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly || isEvaluation">Finalizar</b-btn>
        </template>
    </form-wizard>
  </b-form>
</template>

<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";
import InformationGeneral from '@/components/LegalAspects/ContractLessee/InformationGeneral.vue';
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import ModalObservations from "../EvaluationContracts/ModalObservations.vue"
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    FormWizard,
    TabContent,
    WizardStep,
    InformationGeneral,
    VueTextarea,
    VueCheckboxSimple,
    ModalObservations
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    userDataUrl: { type: String, default: "" },
    isEvaluation: { type: Boolean, default: false },
    typesEvaluation: {
      type: Array,
      default: function() {
        return [];
      }
    },
    typesRating: {
      type: Array,
      default: function() {
        return [];
      }
    },
    evaluation: {
      default() {
        return {
          information_contract_lessee_id: '',
          name: '',
          type: '',
          evaluators_id: '',
          interviewees: [],
          objectives: [
          ]
        };
      }
    }
  },
  mounted() {
    this.$refs.wizardFormEvaluation.activateAll();
  },
  watch: {
    evaluation() {
      this.loading = false;
      this.form = Form.makeFrom(this.evaluation, this.method);
    },
    'form.information_contract_lessee_id' () {
        this.fetchContractor()
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.evaluation, this.method),
        contractDataUrl: '/selects/contractors',
        contractor_information: {
          nit: '',
          type: '',
          business_name: '',
          phone: '',
          address: '',
          legal_representative_name: '',
          SG_SST_name: '',
          number_workers: '',
          high_risk_work: '',
          social_reason: ''
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
          this.$router.push({ name: "legalaspects-evaluations" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addInterviewed() {
        this.form.interviewees.push({
            name: '',
            position: ''
        })
    },
    removeInterviewed(index)
    {
      if (this.form.interviewees[index].id != undefined)
        this.form.delete.interviewees.push(this.form.interviewees[index].id)

      this.form.interviewees.splice(index, 1)
    },
    fetchContractor()
    {
        axios.get(`/legalAspects/contract/${this.form.information_contract_lessee_id}`)
        .then(response => {
            this.contractor_information = response.data.data
        })
        .catch(error => {
            Alerts.error('Error', 'Hubo un problema recolectando la información');
        });
    },
    addObjetive() {
        this.form.objectives.push({
            key: new Date().getTime(),
            description: '',
            subobjectives: []
        })
    },
    removeObjetive(index)
    {
      if (this.form.objectives[index].id != undefined)
        this.form.delete.objectives.push(this.form.objectives[index].id)

      this.form.objectives.splice(index, 1)
    },
    addSubobjective(index)
    {
        this.form.objectives[index].subobjectives.push({
            key: new Date().getTime(),
            description: '',
            items: []
        })
    },
    removeSubobjective(indexObj, index)
    {
      if (this.form.objectives[indexObj].subobjectives[index].id != undefined)
        this.form.delete.subobjectives.push(this.form.objectives[indexObj].subobjectives[index].id)

      this.form.objectives[indexObj].subobjectives.splice(index, 1)
    },
    addItem(indexObj, indexSub) 
    {
      let ratings = []

      _.forIn(this.typesRating, (value, key) => {
        this.$set(ratings, value.id, {
          item_id: '',
          type_rating_id: value.id,
          apply: 'SI',
          value: ''
        })
      });

      this.form.objectives[indexObj].subobjectives[indexSub].items.push({
          key: new Date().getTime(),
          description: '',
          ratings: ratings
      })
    },
    removeItem(indexObj, indexSub, index)
    {
      if (this.form.objectives[indexObj].subobjectives[indexSub].items[index].id != undefined)
        this.form.delete.items.push(this.form.objectives[indexObj].subobjectives[indexSub].items[index].id)

      this.form.objectives[indexObj].subobjectives[indexSub].items.splice(index, 1)
    },
    pushRemoveObservation(value)
    {
      this.form.delete.observations.push(value)
    }
  }
};
</script>
