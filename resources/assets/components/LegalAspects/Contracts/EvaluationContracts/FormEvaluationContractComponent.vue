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
                        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.contract_id" :selected-object="form.multiselect_contract_id" name="contract_id" label="Contratista" placeholder="Seleccione la contratista" :url="contractDataUrl" :error="form.errorsFor('contract_id')">
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
                                <b-col cols="10" class="d-flex justify-content-between"> Entrevistados </b-col>
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

            <tab-content title="Evaluación">
                <loading :display="form.evaluation == undefined"/>
                <div class="col-md-12" v-if="form.evaluation != undefined">
                <blockquote class="blockquote text-left pb-10" style="padding-bottom: 5px; padding-top:5px;" v-if="viewOnly">
                    <p class="mb-0"><b>Fecha de evaluación:</b> {{ evaluation.evaluation_date ? evaluation.evaluation_date : 'Sin evaluar'}}</p>
                </blockquote>
                <blockquote class="blockquote text-center">
                    <p class="mb-0">Objetivos de la evaluación</p>
                </blockquote>
                <b-form-row style="padding-top: 15px;">
                    <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                        <template v-for="(objetive, index) in form.evaluation.objectives">
                            <b-card no-body class="mb-2 border-secondary" :key="objetive.key" style="width: 100%;">
                            <b-card-header class="bg-secondary">
                                <b-row>
                                    <b-col cols="10" class="d-flex justify-content-between"> {{ form.evaluation.objectives[index].description }}</b-col>
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
                                    <vue-textarea :disabled="true" class="col-md-12" v-model="form.evaluation.objectives[index].description" label="Descripción" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                                    </b-form-row>
                                    <b-form-row style="padding-top: 15px;">
                                        <template v-for="(subobjective, index2) in objetive.subobjectives" style="padding-right: 15px;">
                                            <b-card no-body class="mb-2 border-secondary" :key="subobjective.key" style="width: 100%;">
                                            <b-card-header class="bg-secondary">
                                                <b-row>
                                                    <b-col cols="10" class="d-flex justify-content-between"> {{ form.evaluation.objectives[index].subobjectives[index2].description }}</b-col>
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
                                                    <vue-textarea :disabled="true" class="col-md-12" v-model="form.evaluation.objectives[index].subobjectives[index2].description" label="Descripción" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                                                    </b-form-row>
                                                    <b-form-row style="padding-top: 15px;">
                                                    <div class="table-responsive" style="padding-right: 15px;">
                                                        <table class="table table-bordered table-hover">
                                                        <thead class="bg-secondary">
                                                            <tr>
                                                            <th scope="col" class="align-middle text-center">#</th>
                                                            <th scope="col" class="align-middle">Descripción</th>
                                                            <template v-for="(type, indexType) in form.evaluation.types_rating">
                                                                <th scope="col" class="align-middle text-center" :key="indexType">{{ type.name }}</th>
                                                            </template>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index3) in subobjective.items">
                                                            <tr :key="index3">
                                                                <td class="align-middle">
                                                                    <center>
                                                                    <modal-observations v-model="form.evaluation.objectives[index].subobjectives[index2].items[index3].observations" :item-id="item.id" :view-only="viewOnly" @removeObservation="pushRemoveObservation" :form="form" :prefixIndex="`evaluation.objectives.${index}.subobjectives.${index2}.items.${index3}.observations`"/>
                                                                    <modal-file v-model="form.evaluation.objectives[index].subobjectives[index2].items[index3].files" :item-id="item.id" :view-only="viewOnly" @removeFile="pushRemoveFile" :form="form" :prefixIndex="`evaluation.objectives.${index}.subobjectives.${index2}.items.${index3}.files`"/>
                                                                    </center>
                                                                </td>
                                                                <td style="padding: 0px;">
                                                                <vue-textarea :disabled="true" class="col-md-12" v-model="form.evaluation.objectives[index].subobjectives[index2].items[index3].description" label="" name="description" placeholder="Descripción"  rows="1"></vue-textarea>
                                                                </td>
                                                                <template v-for="(type, indexType) in form.evaluation.types_rating">
                                                                <td :key="indexType" class="align-middle text-nowrap" style="padding: 0px;">
                                                                    <center>

                                                                    <template v-if="form.evaluation.objectives[index].subobjectives[index2].items[index3].ratings[type.id].apply == 'SI'">

                                                                    <vue-radio v-if="!viewOnly" :checked="form.evaluation.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value" class="col-md-12" v-model="form.evaluation.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value" :options="[{'text':'SI','value':'SI'},{'text':'NO','value':'NO'},{'text':'N/A','value': 'N/A'}]" :name="`value${item.id}${type.id}`" label="" :error="form.errorsFor(`evaluation.objectives.${index}.subobjectives.${index2}.items.${index3}.ratings.${type.id}.value`)"></vue-radio>

                                                                    <template v-if="viewOnly">
                                                                        {{ form.evaluation.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value ? (form.evaluation.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value == 'pending' ? 'NO' : form.evaluation.objectives[index].subobjectives[index2].items[index3].ratings[type.id].value) : 'N/A' }}
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
                                                            <tr v-if="viewOnly">
                                                                <td colspan="2">Porcentaje de cumplimiento</td>
                                                                <td v-for="(report, indexR) in subobjective.report" :key="indexR" class="bg-secondary align-middle text-center">
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

            <tab-content title="Historial" v-if="viewOnly">
                <div class="col-md-12">
                    <blockquote class="blockquote text-center">
                        <p class="mb-0">Fechas de modificaciones</p>
                    </blockquote>
                    <div class="col-md">
                        <b-card no-body>
                            <b-card-body>
                                <vue-table
                                    configName="legalaspects-evaluations-contracts-histories"
                                    :modelId="form.id ? form.id : -1"
                                    ></vue-table>
                            </b-card-body>
                        </b-card>
                    </div>
                </div>
            </tab-content>

            <template slot="footer" slot-scope="props">
                <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>
                <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
                <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>
                <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
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
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Alerts from '@/utils/Alerts.js';
import EvaluationTypesRating from '../Evaluations/EvaluationTypesRating.vue';
import InformationGeneral from '@/components/LegalAspects/Contracts/ContractLessee/InformationGeneral.vue';
import ModalObservations from "./ModalObservations.vue"
import ModalFile from "./ModalFile.vue"
import Loading from "@/components/Inputs/Loading.vue";

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    FormWizard,
    TabContent,
    WizardStep,
    VueTextarea,
    VueCheckboxSimple,
    EvaluationTypesRating,
    InformationGeneral,
    ModalObservations,
    ModalFile,
    Loading
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    userDataUrl: { type: String, default: "" },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    typesRating: {
      type: Array,
      default: function() {
        return [];
      }
    },
    evaluation: {
      default() {
        return {
            contract_id: '',
            evaluation_id: '',
            evaluators_id: '',
            interviewees: [],
            evaluation: {
                objectives: []
            }
        };
      }
    }
  },
  mounted() {
        setTimeout(() => {
            this.$refs.wizardFormEvaluation.activateAll();
        }, 4000)
  },
  watch: {
    evaluation() {
      this.loading = false;
      this.form = Form.makeFrom(this.evaluation, this.method);
    },
    'form.contract_id' () {
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
            classification: '',
            type: '',
            business_name: '',
            phone: '',
            address: '',
            legal_representative_name: '',
            environmental_management_name: '',
            economic_activity_of_company: '',
            arl: '',
            SG_SST_name: '',
            risk_class: '',
            number_workers: '',
            high_risk_work: '',
            social_reason: ''
        }
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

      this.form.clearFilesBinary();

      this.form.evaluation.objectives.forEach((objetive, keyObj) => {
          objetive.subobjectives.forEach((subobjetive, keySubObj) => {
              subobjetive.items.forEach((item, keyItem) => {
                  item.files.forEach((file, keyFile) => {
                      this.form.addFileBinary(`${keyObj}_${keySubObj}_${keyItem}_${keyFile}`, file.file)
                  });
              });
          });
      });
                        
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.back()
        })
        .catch(error => {
          this.loading = false;
        });
    },
    fetchContractor()
    {
        axios.get(`/legalAspects/contracts/${this.form.contract_id}`)
        .then(response => {
            this.contractor_information = response.data.data
        })
        .catch(error => {
            Alerts.error('Error', 'Hubo un problema recolectando la información');
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
    pushRemoveObservation(value)
    {
      this.form.delete.observations.push(value)
    },
    pushRemoveFile(value)
    {
      this.form.delete.files.push(value)
    }
  }
};
</script>
