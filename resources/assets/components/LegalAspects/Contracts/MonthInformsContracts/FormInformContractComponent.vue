<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <form-wizard ref="wizardForminform">
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
                        <vue-ajax-advanced-select :disabled="viewOnly || auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']" class="col-md-12" v-model="form.contract_id" :selected-object="form.multiselect_contract_id" name="contract_id" label="Contratista" placeholder="Seleccione la contratista" :url="contractDataUrl" :error="form.errorsFor('contract_id')">
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
                    <label class="col-md-2"><b>Período a evaluar</b></label>
                </b-form-row>
                <b-form-row>
                    <vue-advanced-select class="col-md-2" :disabled="viewOnly" v-model="form.year" :options="yearsOptions" :searchable="false" name="years" placeholder="Año" :multiple="false"></vue-advanced-select>
                    <vue-advanced-select class="col-md-2" :disabled="viewOnly" v-model="form.month" :options="monthsOptions" @input="periodValid()" :searchable="false" name="years" placeholder="Mes" :multiple="false"></vue-advanced-select>
                </b-form-row>
                <b-form-row>
                    <vue-input v-if="viewOnly || isEdit" disabled class="col-md-4" v-model="form.inform_date" label="Fecha de presentación" type="text" name="inform_date" placeholder="Fecha de presentación"></vue-input>
                  <vue-textarea :disabled="viewOnly" class="col-md-8" v-model="form.Objective_inform" label="Objetivo del informe" name="Objective_inform" placeholder="Objetivo del informe"  rows="1"></vue-textarea>
                </b-form-row>

            </tab-content>

            <tab-content title="Evaluación">
                <loading :display="form.inform == undefined"/>
                <div class="col-md-12" v-if="form.inform != undefined">
                <blockquote class="blockquote text-left pb-10" style="padding-bottom: 5px; padding-top:5px;" v-if="viewOnly">
                    <p class="mb-0"><b>Fecha de realización:</b> {{ inform.inform_date ? inform.inform_date : 'Sin evaluar'}}</p>
                </blockquote>

                <blockquote class="blockquote text-center">
                    <p class="mb-0">Temas del Informe</p>
                </blockquote>
                <b-form-row style="padding-top: 15px;">
                    <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                      <template v-for="(objetive, index) in form.inform.themes">
                        <b-card no-body class="mb-2 border-secondary" :key="objetive.key" style="width: 100%;">
                          <b-card-header class="bg-secondary">
                              <b-row>
                                  <b-col cols="10" class="d-flex justify-content-between"> {{ form.inform.themes[index].description }}</b-col>
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
                              <b-form-row style="padding-top: 15px;">
                                <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
                                  <b-card-body>
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-secondary">
                                            <tr>
                                                <th scope="col" class="align-middle text-center">#</th>
                                                <th scope="col" class="align-middle">Descripción</th>
                                                <th colspan="3" scope="col" class="align-middle text-center" ></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <template v-for="(item, index2) in objetive.items">
                                              <tr :key="index2" style="width:100%">
                                                  <td style="width:5%" class="align-middle">
                                                      <center>
                                                      <modal-observations v-model="form.inform.themes[index].items[index2].observations" :item-id="item.id" :view-only="viewOnly" @removeObservation="pushRemoveObservation" :form="form" :prefixIndex="`inform.themes.${index}.observations`"/>
                                                      <modal-file v-model="form.inform.themes[index].items[index2].files" :item-id="item.id" :view-only="viewOnly" @removeFile="pushRemoveFile" :form="form" :prefixIndex="`inform.themes${index}.items${index2}.files`"/>
                                                      <b-btn @click="showModal(`modalPlan${index2}`)" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Ver plan de acción"><span class="ion ion-md-paper"></span></b-btn>
                                                      </center>
                                                      <b-modal :ref="`modalPlan${index2}`" :hideFooter="true" :id="`modals-default-${index2+1}`" class="modal-top" size="lg" @hidden="removed(index, index2)">
                                                      <div slot="modal-title">
                                                          Plan de acción<br>
                                                          <small class="text-muted">Crea planes de acción.</small>
                                                      </div>

                                                      <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                                          <action-plan-component
                                                          :is-edit="!viewOnly"
                                                          :view-only="viewOnly"
                                                          :form="form"
                                                          :prefix-index="`inform.themes${index}.items${index2}-actionPlan`"
                                                          :action-plan-states="actionPlanStates"
                                                          v-model="form.inform.themes[index].items[index2].actionPlan"
                                                          :action-plan="form.inform.themes[index].items[index2].actionPlan"/>
                                                      </b-card>
                                                      <br>
                                                      <div class="row float-right pt-12 pr-12y">
                                                          <b-btn variant="primary" @click="hideModal(`modalPlan${index2}`)">Cerrar</b-btn>
                                                      </div>
                                                      </b-modal>
                                                  </td>                  
                                                  <td colspan="3" style="width:40%; padding: 0px;">
                                                      <vue-textarea :disabled="true" class="col-md-12" v-model="form.inform.themes[index].items[index2].description" label="" name="description" placeholder="Descripción" rows="3"></vue-textarea>
                                                  </td>
                                                  <td class="align-middle text-nowrap" style="padding: 0px; width:55%">
                                                    <b-row class="col-md-12">
                                                      <b-col>
                                                        <vue-input :disabled="viewOnly" v-model="form.inform.themes[index].items[index2].programmed" label="Programado" type="number" name="name" :error="form.errorsFor('name')" @input="calculatePorcentage(index, index2)"></vue-input>
                                                      </b-col>
                                                      <b-col>
                                                        <vue-input :disabled="viewOnly" v-model="form.inform.themes[index].items[index2].executed" label="Ejecutado" type="number" name="name" :error="form.errorsFor('name')" @input="calculatePorcentage(index, index2)"></vue-input>
                                                      </b-col>
                                                      <b-col :key="form.inform.themes[index].items[index2].compliance">
                                                        <vue-input :disabled="true" v-model="form.inform.themes[index].items[index2].compliance" label="% Cumplimiento" type="number" name="name" :error="form.errorsFor('name')"></vue-input>
                                                      </b-col>
                                                      <b-btn @click="showModal(`modalHistory${index2}`, true, form.inform.themes[index].items[index2].id)" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Ver historial"><span class="ion ion-ios-copy"></span></b-btn>
                                                    </b-row>
                                                    <b-modal :ref="`modalHistory${index2}`" :hideFooter="true" :id="`modals-default-${index2+1}`" class="modal-top modal-item" size="lg">
                                                      <div slot="modal-title">
                                                          Histórico<br>
                                                      </div>
                                                      <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                                          <table>
                                                            <thead>
                                                              <tr style="width:100%">
                                                                <td style="width:30%">Item</td>
                                                                <template v-for="(month, indexM) in history.headings">
                                                                  <td :key="indexM">{{month}}</td>
                                                                </template>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <tr style="width:100%">
                                                                <td style="width:30%">
                                                                  <vue-textarea :disabled="true" class="col-md-12" v-model="form.inform.themes[index].items[index2].description" label="" name="description" rows="3"></vue-textarea>
                                                                </td>
                                                                <template v-for="(executed, indexE) in history.answers">
                                                                  <td style="vertical-align: middle;" :key="indexE"><center>{{executed}}</center></td>
                                                                </template>
                                                              </tr>
                                                            </tbody>
                                                          </table>
                                                      </b-card>
                                                      <br>
                                                      <div class="row float-right pt-12 pr-12y">
                                                          <b-btn variant="primary" @click="hideModal(`modalHistory${index2}`)">Cerrar</b-btn>
                                                      </div>
                                                      </b-modal>
                                                  </td>
                                              </tr>
                                          </template>
                                        </tbody>
                                    </table>
                                  </b-card-body>
                                </b-card>
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
                            <vue-textarea class="col-md-12" :disabled="viewOnly" v-model="form.observation" label="Observación (Opcional)" name="observation" placeholder="Observación" help-text="La información plasmada en este campo se adjuntara en el correo que se le enviara al contratista al culminar la evaluación." rows="4"></vue-textarea>
                            </b-card>
                    </b-col>
                </b-row>
            </tab-content>

            <template slot="footer" slot-scope="props">
                <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>
                <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
                <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>                
                <!--<b-btn @click="submit(false)" :disabled="loading" variant="primary" v-if="!viewOnly">Guardar y continuar</b-btn>-->
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Alerts from '@/utils/Alerts.js';
import InformationGeneral from '@/components/LegalAspects/Contracts/ContractLessee/InformationGeneral.vue';
import ModalObservations from "./ModalObservations.vue"
import ModalFile from "./ModalFile.vue"
import Loading from "@/components/Inputs/Loading.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

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
    InformationGeneral,
    ModalObservations,
    ModalFile,
    Loading,
    ActionPlanComponent,
    VueAdvancedSelect
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
    actionPlanStates: {
      type: Array,
      default: function() {
        return [];
      }
    },
    inform: {
      default() {
        return {
            contract_id: '',
            inform_id: '',
            observation: '',
            Objective_inform: '',
            year: '',
            month: '',
            inform: {
                themes: []
            },
            delete: {
              observations: [],
              files: []
            }
        };
      }
    }
  },
  mounted() {
        setTimeout(() => {
            this.$refs.wizardForminform.activateAll();
            this.verify = true
        }, 4000)

  },
  watch: {
    inform() {
      this.loading = false;
      this.form = Form.makeFrom(this.inform, this.method);
    },
    'form.contract_id' () {
        this.fetchContractor()
    },
    /*'form.month' () {
      if (!this.isEdit || !this.viewOnly)
        this.periodValid()
    }*/
  },
  data() {
    return {
        cargando: false,
        yearsOptions: [],
        monthsOptions: [],
        resumenList: [],
        loading: this.isEdit,
        form: Form.makeFrom(this.inform, this.method),
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
        },
        autoSave: '',
        textBlock: 'Cargando...',
        verify: false,
        history: []
        
    };
  },
  computed: {
    html() {
        return `<div class="sk-folding-cube sk-primary"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div><h5 class="text-center mb-0">${this.textBlock}</h5>`
    }
  },
  created() {
    /*if (!this.viewOnly) {
      this.autoSave = setInterval(this.saveAuto, 900000);
    }*/
    
    this.fetchSelect('yearsOptions', '/selects/inform/years')
    this.fetchSelect('monthsOptions', '/selects/inform/month')
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

          this.form.inform.themes.forEach((theme, keyObj) => {
            theme.items.forEach((item, keyItem) => {
             item.files.forEach((file, keyFile) => {
                  this.form.addFileBinary(`${keyObj}_${keyItem}_${keyFile}`, file.file)
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
    removed(index, index2) {
        let keys = [];

        this.form.inform.themes[index].items[index2].actionPlan.activities.forEach((activity, keyAct) => {
          if (activity.description && activity.responsible_id && activity.expiration_date && activity.state)
          {
            keys.push(activity);
          }
        });

        this.form.inform.themes[index].items[index2].actionPlan.activities.splice(0);

        keys.forEach((item, key) => {
            this.form.inform.themes[index].items[index2].actionPlan.activities.push(item)
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
    pushRemoveObservation(value)
    {
      this.form.delete.observations.push(value)
    },
    pushRemoveFile(value)
    {
      this.form.delete.files.push(value)
    },
    showModal(ref, history = false, item_id = 0) {
      if (history)
      {
        this.historyItem(item_id)
      }
        this.$refs[ref][0].show()
    },
    hideModal(ref) {
        this.$refs[ref][0].hide()
    },
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
    calculatePorcentage(index, index2)
    {
      if (this.form.inform.themes[index].items[index2].programmed != undefined && this.form.inform.themes[index].items[index2].executed != undefined)
      {
        if (this.form.inform.themes[index].items[index2].programmed !== '' && this.form.inform.themes[index].items[index2].executed !== '')
        {
          this.form.inform.themes[index].items[index2].compliance = Math.round((this.form.inform.themes[index].items[index2].executed / this.form.inform.themes[index].items[index2].programmed) * 100);
        }
        else
        {
          this.form.inform.themes[index].items[index2].compliance = ''
        }
      }
      else
      {
        this.form.inform.themes[index].items[index2].compliance = ''
      }
    },
    periodValid()
    {
      if (this.verify)
      {
        this.postData = Object.assign({}, {year: this.form.year}, {month: this.form.month}, {contract: this.form.contract_id},
        {inform: this.form.inform_id});

        axios.post('/legalAspects/informContract/periodExist', this.postData)
          .then(response => {
              
          }).catch(error => {
              Alerts.error('Error', 'Este período ya ha sido evaluado para este contratista, por favor seleccione otro período');
          });
      }
    },
    historyItem($index)
    {
      this.postData = Object.assign({}, {year: this.form.year}, {contract: this.form.contract_id},
      {inform: this.form.inform_id}, {item_id: $index});

      axios.post('/legalAspects/informContract/historyItemQualification', this.postData)
        .then(response => {
          this.history = response.data
        }).catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
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

.modal-item {
  .modal-dialog {
    min-width: 95% !important;
  }
}
</style>
