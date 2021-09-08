<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <form-wizard ref="wizardFormEvaluation">
            <!-- Allow html in tab title (this template required for the proper styling) -->
            <template slot="step" slot-scope="props">
                <wizard-step :tab="props.tab" @click.native="props.navigateToTab(props.index)" @keyup.enter.native="props.navigateToTab(props.index)" :transition="props.transition" :index="props.index">
                <span slot="title" :class="{'text-danger':props.tab.validationError}" v-html="props.tab.title"></span>
                </wizard-step>
            </template>
            
            <tab-content title="General">                
                <b-form-row>
                    <vue-radio :disabled="viewOnly" :checked="form.type" class="col-md-12" v-model="form.type" :options="[{'text':'Inicial','value':'Inicial'},{'text':'Seguimiento','value': 'Seguimiento'}]" name="type" :error="form.errorsFor('type')" label="Tipo de evaluación"></vue-radio>
                </b-form-row>
                <b-form-row>
                    <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.evaluators_id" :selected-object="form.multiselect_evaluators_id" name="evaluators_id" label="Evaluadores" placeholder="Seleccione los evaluadores" :url="userDataUrl" :error="form.errorsFor('evaluators_id')" :multiple="true" :allowEmpty="true">
                        </vue-ajax-advanced-select>
                </b-form-row>

                <b-form-row>
                    <div class="col-md-12" v-if="!viewOnly">
                    <div class="float-right" style="padding-top: 10px;">
                        <b-btn variant="primary" @click.prevent="addInterviewed()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Otros Participantes</b-btn>
                    </div>
                    </div>
                </b-form-row>

                <b-form-row style="padding-top: 15px;">
                    <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 300px; padding-right: 15px; width: 100%;">
                    <b-card no-body class="mb-2 border-secondary" style="width: 100%;" v-if="form.interviewees && form.interviewees.length > 0">
                        <b-card-header class="bg-secondary">
                            <b-row>
                                <b-col cols="10" class="d-flex justify-content-between"> Otros Participantes </b-col>
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
                                        <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Participante" @click.prevent="removeInterviewed(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                                    </div>
                                    </div>
                                </b-form-row>
                                <b-form-row>
                                    <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.interviewees[index].name" label="Nombre" type="text" name="name" :error="form.errorsFor(`interviewees.${index}.name`)" placeholder="Nombre"></vue-input>
                                    <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.interviewees[index].position" :label="keywordCheck('position')" type="text" name="position" :error="form.errorsFor(`interviewees.${index}.position`)" placeholder="Cargo"></vue-input>
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

                <div id="fixedbutton">
                    <b-btn variant="primary" type="button" @click="calculateResumen" title="Resumen">
                        <i class="ion ion-ios-list"></i>
                    </b-btn>
                </div>

                <!--<b-modal ref="modalPending" :hideFooter="true" id="modalPending" class="modal-top" size="lg">
                    <div slot="modal-title">
                      Items pendientes por calificar
                    </div>
                     <b-card bg-variant="transparent" title="" class="mb-3 box-shadow-none">
                        <loading :display="cargando"/>
                        <template v-if="!cargando">
                            <div v-for="stage in resumenList" >
                                <blockquote class="blockquote text-center">
                                    <p class="mb-0">{{ stage.description }}</p>
                                </blockquote>
                                 <b-card no-body class="pb-1 mb-2" v-for="(criterion, keySub) in stage.criterion" :key="`sub-${keySub}`">
                                  <div class="row no-gutters align-items-center">
                                    <div class="col-12 col-md-7 px-4 pt-4">
                                      <p class="text-dark font-weight-semibold" style="font-size: 10px;">{{ criterion.description }}</p><br>
                                    </div>
                                    <div class="col-4 col-md-1 text-muted small">
                                     <strong>Items</strong> <br> {{ `${criterion.itemsQualify}/${criterion.itemsTotal}` }}
                                    </div>
                                    <div class="col-4 col-md-3">
                                      <div class="text-right text-muted small">{{ Math.abs((criterion.itemsQualify/criterion.itemsTotal) * 100).toFixed(0) }}%</div>
                                      <b-progress :value="parseFloat(Math.abs((criterion.itemsQualify/criterion.itemsTotal) * 100).toFixed(0))" height="6px" :variant="criterion.itemsTotal == criterion.itemsQualify ? 'success' : 'danger'" />
                                    </div>
                                    <div class="col-4 col-md-1 text-muted small text-center">
                                        <span v-if="criterion.itemsTotal == criterion.itemsQualify" class="ion ion-md-checkbox text-success"></span>
                                        <span v-else class="ion ion-md-close-circle text-danger"></span>
                                    </div>
                                  </div>
                                  <br>
                                </b-card>
                            </div>
                        </template>
                    </b-card>

                    <div class="row float-right pt-12 pr-12y">
                      <b-btn variant="primary" @click="$refs.modalPending.hide()">Cerrar</b-btn>
                    </div>
                </b-modal>-->

                <blockquote class="blockquote text-center">
                    <p class="mb-0">Temas de la evaluación</p>
                </blockquote>
                <b-form-row style="padding-top: 15px;">
                    <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                        <template v-for="(stage, index) in form.evaluation.stages">
                            <b-card no-body class="mb-2 border-secondary" :key="stage.key" style="width: 100%;">
                            <b-card-header class="bg-secondary">
                                <b-row>
                                    <b-col cols="10" class="d-flex justify-content-between"> {{ form.evaluation.stages[index].description }}</b-col>
                                    <b-col cols="2">
                                        <div class="float-right">
                                        <b-button-group>
                                            <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + stage.key+'-12'" variant="link">
                                            <span class="collapse-icon"></span>
                                            </b-btn>
                                        </b-button-group>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-header>
                            <b-collapse :id="`accordion${stage.key}-12`" visible :accordion="`accordion-123543`">
                                <b-card-body>
                                    <b-form-row>
                                    <vue-textarea :disabled="true" class="col-md-12" v-model="form.evaluation.stages[index].description" label="Descripción" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                                    </b-form-row>
                                    <b-form-row style="padding-top: 15px;">
                                        <template v-for="(criterion, index2) in stage.criterion" style="padding-right: 15px;">
                                            <b-card no-body class="mb-2 border-secondary" :key="criterion.key" style="width: 100%;">
                                            <b-card-header class="bg-secondary">
                                                <b-row>
                                                    <b-col cols="10" class="d-flex justify-content-between"> {{ form.evaluation.stages[index].criterion[index2].description }}</b-col>
                                                    <b-col cols="2">
                                                        <div class="float-right">
                                                        <b-button-group>
                                                            <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + criterion.key+'-12'" variant="link">
                                                            <span class="collapse-icon"></span>
                                                            </b-btn>
                                                        </b-button-group>
                                                        </div>
                                                    </b-col>
                                                </b-row>
                                            </b-card-header>
                                            <b-collapse :id="`accordion${criterion.key}-12`" visible :accordion="`accordion-123456`">
                                                <b-card-body>
                                                    <b-form-row>
                                                    <vue-textarea :disabled="true" class="col-md-12" v-model="form.evaluation.stages[index].criterion[index2].description" label="Descripción" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                                                    </b-form-row>
                                                    <b-form-row style="padding-top: 15px;">
                                                    <div class="table-responsive" style="padding-right: 15px;">
                                                        <table class="table table-bordered table-hover">
                                                        <thead class="bg-secondary">
                                                            <tr>
                                                            <th scope="col" class="align-middle text-center">#</th>
                                                            <th scope="col" class="align-middle">Descripción</th>
                                                            <th scope="col" class="align-middle">Cumplimiento</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-for="(item, index3) in criterion.items">
                                                            <tr :key="index3">
                                                                <td class="align-middle">
                                                                    <center>
                                                                    <modal-observations v-model="form.evaluation.stages[index].criterion[index2].items[index3].observations" :item-id="item.id" :view-only="viewOnly" @removeObservation="pushRemoveObservation" :form="form" :prefixIndex="`evaluation.stages.${index}.criterion.${index2}.items.${index3}.observations`"/>
                                                                    <modal-file v-model="form.evaluation.stages[index].criterion[index2].items[index3].files" :item-id="item.id" :view-only="viewOnly" @removeFile="pushRemoveFile" :form="form" :prefixIndex="`evaluation.stages.${index}.criterion.${index2}.items.${index3}.files`"/>
                                                                    <b-btn @click="showModal(`modalPlan${index}-${index2}-${index3}`)" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Ver plan de acción"><span class="ion ion-md-paper"></span></b-btn>
                                                                    </center>
                                                                    <b-modal :ref="`modalPlan${index}-${index2}-${index3}`" :hideFooter="true" :id="`modals-default-${index+1}${index2}${index3}`" class="modal-top" size="lg" @hidden="removed(index, index2, index3)">
                                                                    <div slot="modal-title">
                                                                      Plan de acción<br>
                                                                      <small class="text-muted">Crea planes de acción.</small>
                                                                    </div>

                                                                    <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                                                      <action-plan-component
                                                                        :is-edit="!viewOnly"
                                                                        :view-only="viewOnly"
                                                                        :form="form"
                                                                        :prefix-index="`evaluation.stages.${index}.criterion.${index2}.items.${index3}.actionPlan`"
                                                                        :action-plan-states="actionPlanStates"
                                                                        v-model="form.evaluation.stages[index].criterion[index2].items[index3].actionPlan"
                                                                        :action-plan="form.evaluation.stages[index].criterion[index2].items[index3].actionPlan"/>
                                                                    </b-card>
                                                                    <br>
                                                                    <div class="row float-right pt-12 pr-12y">
                                                                      <b-btn variant="primary" @click="hideModal(`modalPlan${index}-${index2}-${index3}`)">Cerrar</b-btn>
                                                                    </div>
                                                                  </b-modal>
                                                                </td>                  
                                                                <td style="padding: 0px;">
                                                                <vue-textarea :disabled="true" class="col-md-12" v-model="form.evaluation.stages[index].criterion[index2].items[index3].description" label="" name="description" placeholder="Descripción"  rows="1"></vue-textarea>
                                                                </td>
                                                                <td class="align-middle text-nowrap" style="padding: 0px;">
                                                                    <center>
                                                                        <vue-radio v-if="!viewOnly" :checked="form.evaluation.stages[index].criterion[index2].items[index3].value" class="col-md-12" v-model="form.evaluation.stages[index].criterion[index2].items[index3].value" :options="[{'text':'Cumple','value':'Cumple'},{'text':'Parcial','value': 'Parcial'}, {'text':'No Cumple','value':'No Cumple'}]" :name="`value${item.id}`" label="" :error="form.errorsFor(`evaluation.stages.${index}.criterion.${index2}.items.${index3}.value`)"></vue-radio>

                                                                        <template v-else>
                                                                        {{ form.evaluation.stages[index].criterion[index2].items[index3].value ? form.evaluation.stages[index].criterion[index2].items[index3].value : 'Sin calificar' }}
                                                                    </template>
                                                                    </center>
                                                                </td>
                                                            </tr>
                                                            </template>
                                                            <!--<tr v-if="viewOnly">
                                                                <td colspan="2">Porcentaje de cumplimiento</td>
                                                                <td v-for="(report, indexR) in criterion.report" :key="indexR" class="bg-secondary align-middle text-center">
                                                                    {{report.percentage}}%
                                                                </td>
                                                            </tr>-->
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

            <tab-content title="Observación">
                <b-row>
                    <b-col>
                        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                            <vue-textarea class="col-md-12" :disabled="viewOnly" v-model="form.observation" label="Observación (Opcional)" name="observation" placeholder="Observación" rows="4"></vue-textarea>
                            </b-card>
                    </b-col>
                </b-row>
            </tab-content>

            <tab-content v-if="isEdit || viewOnly"  title="Reporte">
                <center><b-row>
                    <b-col>
                        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                            <radar-component :key="test" :chartData="chartData" ref="radar"></radar-component>
                        </b-card>
                    </b-col>
                </b-row></center>
            </tab-content>

            <template slot="footer" slot-scope="props">
                <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>
                <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
                <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>                
                <b-btn @click="submit(false)" :disabled="loading" variant="primary" v-if="!viewOnly">Guardar y continuar</b-btn>
                <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
            </template>
        </form-wizard>

        <b-card>
          <BlockUI message="" :html="html" v-if="loading" />
        </b-card>
    </b-form>
</template>

<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>
<style src="@/vendor/libs/spinkit/spinkit.scss" lang="scss"></style>

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
import ModalObservations from "./ModalObservations.vue"
import ModalFile from "./ModalFile.vue"
import Loading from "@/components/Inputs/Loading.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import RadarComponent from '@/components/Chartjs/ChartRadar.vue';

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
    ModalObservations,
    ModalFile,
    Loading,
    ActionPlanComponent,
    RadarComponent
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    userDataUrl: { type: String, default: "" },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    actionPlanStates: {
      type: Array,
      default: function() {
        return [];
      }
    },
    evaluation: {
      default() {
        return {
            evaluation_id: '',
            type: '',
            evaluators_id: '',
            interviewees: [],
            observation: '',
            evaluation: {
                stages: []
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
      this.labels_report = this.form.evaluation.labels_report;

      this.chartData = {
            labels: this.labels_report,
            datasets: [{
                label: 'Base',
                backgroundColor: 'rgba(0, 0, 255, 0)',
                borderColor: '#ff0000',
                pointBackgroundColor: '#ff0000',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#ff0000',
                data: this.form.evaluation.report_values_base,
                borderWidth: 1
            }, {
                label: 'Resultado',
                backgroundColor: 'rgba(255, 0, 0, 0)',
                borderColor: '#0000ff',
                pointBackgroundColor: '#0000ff',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#0000ff',
                data: this.form.evaluation.report_values_result,
                borderWidth: 1
            }]
        }
        this.test = !this.test;
    }
  },
  data() {
    return {
        cargando: false,
        resumenList: [],
        loading: this.isEdit,
        form: Form.makeFrom(this.evaluation, this.method),
        labels_report : [],
        autoSave: '',
        textBlock: 'Cargando...',
        chartData: {},
        test: true
    };
  },
  computed: {
    html() {
        return `<div class="sk-folding-cube sk-primary"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div><h5 class="text-center mb-0">${this.textBlock}</h5>`
    } 
  },
  created() {
    if (!this.viewOnly) {
      this.autoSave = setInterval(this.saveAuto, 900000);
    }
  },
  beforeDestroy() {
    clearInterval( this.autoSave );
  },
  methods: {
    saveAuto() {
        this.submit(false);
    },
    submit(redirect = true) {
        this.textBlock = 'Guardando...';

        if (!this.loading)
        {
          this.loading = true;

          let url = this.url;

          this.form.clearFilesBinary();

          this.form.evaluation.stages.forEach((stage, keyObj) => {
              stage.criterion.forEach((criterion, keySubObj) => {
                  criterion.items.forEach((item, keyItem) => {
                      item.files.forEach((file, keyFile) => {
                          this.form.addFileBinary(`${keyObj}_${keySubObj}_${keyItem}_${keyFile}`, file.file)
                      });
                  });
              });
          });
          
          if (this.method == 'POST')
          {
            if (this.form.id)
              this.form.updateMethod('PUT')

            url = !this.form.id ? this.url : `${this.url}/${this.form.id }`;
          }
                            
          this.form
            .submit(url)
            .then(response => {
              this.loading = false;

              if (redirect)
                this.$router.back()
              else
              {
                _.forIn(response.data.data, (value, key) => {
                  this.form[key] = value
                })
              }
            })
            .catch(error => {
              this.loading = false;
            });
        }
    },
    removed(index, index2, index3) {
        let keys = [];

        this.form.evaluation.stages[index].criterion[index2].items[index3].actionPlan.activities.forEach((activity, keyAct) => {
          if (activity.description && activity.responsible_id && activity.expiration_date && activity.state)
          {
            keys.push(activity);
          }
        });

        this.form.evaluation.stages[index].criterion[index2].items[index3].actionPlan.activities.splice(0);

        keys.forEach((item, key) => {
            this.form.evaluation.stages[index].criterion[index2].items[index3].actionPlan.activities.push(item)
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
    },
    showModal(ref) {
        this.$refs[ref][0].show()
    },
    hideModal(ref) {
        this.$refs[ref][0].hide()
    },
    calculateResumen() {
        this.resumenList.splice(0);
        this.cargando = true;
        this.$refs.modalPending.show();

        this.form.evaluation.stages.forEach((stage, keyObj) => {
            let criterion = [];

            stage.criterion.forEach((criterion, keySubObj) => {
                let count = 0

                criterion.items.forEach((item, keyItem) => {
                    let pending = 0

                    _.forIn(item.ratings, (rating, keyRating) => {
                        if (rating.apply == 'SI')
                        {
                            if (!rating.value || rating.value == 'pending')
                                pending++
                        }
                    });

                    if (pending == 0)
                        count++;
                });

                criterion.push({description: criterion.description, itemsTotal: criterion.items.length, itemsQualify: count});
            });

            this.resumenList.push({description: stage.description, criterion: criterion});
        });

        this.cargando = false;
    }
  }
};
</script>

<style lang="scss">
#fixedbutton {
    position: fixed;
    bottom: 0px;
    right: 0px; 
}
</style>
