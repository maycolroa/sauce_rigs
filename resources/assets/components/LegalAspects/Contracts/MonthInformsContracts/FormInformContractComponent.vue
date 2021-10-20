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
                    <label class="col-md-2"><b>Período a evaluar</b></label>
                </b-form-row>
                <b-form-row>
                    <vue-advanced-select class="col-md-2" :disabled="viewOnly" v-model="form.year" :options="yearsOptions" :searchable="false" name="years" placeholder="Año" :multiple="false"></vue-advanced-select>
                    <vue-advanced-select class="col-md-2" :disabled="viewOnly" v-model="form.month" :options="monthsOptions" :searchable="false" name="years" placeholder="Mes" :multiple="false"></vue-advanced-select>
                    <vue-input v-if="viewOnly || isEdit" disabled class="col-md-4" v-model="form.inform_date" label="Fecha de presentación" type="text" name="inform_date" placeholder="Fecha de presentación"></vue-input>
                </b-form-row>
                <b-form-row>
                  <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.objective_inform" label="Objetivo del informe" name="objective_inform" placeholder="Objetivo del informe"  rows="1"></vue-textarea>
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
                                        <template v-for="(theme, index) in form.inform.themes">
                                            <tr :key="index">
                                                <td class="align-middle">
                                                    <center>
                                                    <modal-observations v-model="form.inform.themes[index].observations" :item-id="theme.id" :view-only="viewOnly" @removeObservation="pushRemoveObservation" :form="form" :prefixIndex="`inform.themes.${index}.observations`"/>
                                                    <modal-file v-model="form.inform.themes[index].files" :item-id="theme.id" :view-only="viewOnly" @removeFile="pushRemoveFile" :form="form" :prefixIndex="`inform.themes.${index}.files`"/>
                                                    <b-btn @click="showModal(`modalPlan${index}`)" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Ver plan de acción"><span class="ion ion-md-paper"></span></b-btn>
                                                    </center>
                                                    <b-modal :ref="`modalPlan${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg" @hidden="removed(index)">
                                                    <div slot="modal-title">
                                                        Plan de acción<br>
                                                        <small class="text-muted">Crea planes de acción.</small>
                                                    </div>

                                                    <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                                        <action-plan-component
                                                        :is-edit="!viewOnly"
                                                        :view-only="viewOnly"
                                                        :form="form"
                                                        :prefix-index="`inform.themes.${index}.actionPlan`"
                                                        :action-plan-states="actionPlanStates"
                                                        v-model="form.inform.themes[index].actionPlan"
                                                        :action-plan="form.inform.themes[index].actionPlan"/>
                                                    </b-card>
                                                    <br>
                                                    <div class="row float-right pt-12 pr-12y">
                                                        <b-btn variant="primary" @click="hideModal(`modalPlan${index}`)">Cerrar</b-btn>
                                                    </div>
                                                    </b-modal>
                                                </td>                  
                                                <td style="padding: 0px;">
                                                    <vue-textarea :disabled="true" class="col-md-12" v-model="form.inform.themes[index].description" label="" name="description" placeholder="Descripción"  rows="1"></vue-textarea>
                                                </td>
                                                <td colspan="2" class="align-middle text-nowrap" style="padding: 0px;">
                                                  <b-row class="col-md-12">
                                                    <b-col>
                                                      <vue-input :disabled="viewOnly" v-model="form.inform.themes[index].programmed" label="Programado" type="text" name="name" :error="form.errorsFor('name')"></vue-input>
                                                    </b-col>
                                                    <b-col>
                                                      <vue-input :disabled="viewOnly" v-model="form.inform.themes[index].expected" label="Esperado" type="text" name="name" :error="form.errorsFor('name')"></vue-input>
                                                    </b-col>
                                                    <b-col>
                                                      <vue-input :disabled="viewOnly" v-model="form.inform.themes[index].compliance" label="% Cumplimiento" type="text" name="name" :error="form.errorsFor('name')"></vue-input>
                                                    </b-col>
                                                    <b-btn @click="showModal(`modalHistory${index}`)" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Ver historial"><span class="ion ion-ios-copy"></span></b-btn>
                                                  </b-row>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </b-card-body>
                        </b-card>
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

            <tab-content title="Historial" v-if="viewOnly">
                <div class="col-md-12">
                    <blockquote class="blockquote text-center">
                        <p class="mb-0">Fechas de modificaciones</p>
                    </blockquote>
                    <div class="col-md">
                        <b-card no-body>
                            <b-card-body>
                                <vue-table
                                    configName="legalaspects-informs-contracts-histories"
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
            objective_inform: '',
            year: '',
            month: '',
            inform: {
                themes: []
            }
        };
      }
    }
  },
  mounted() {
        setTimeout(() => {
            this.$refs.wizardForminform.activateAll();
        }, 4000)
  },
  watch: {
    inform() {
      this.loading = false;
      this.form = Form.makeFrom(this.inform, this.method);
    },
    'form.contract_id' () {
        this.fetchContractor()
    }
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
             this.form.addFileBinary(`${keyObj}`, file.file)
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
    removed(index) {
        let keys = [];

        this.form.inform.themes[index].actionPlan.activities.forEach((activity, keyAct) => {
          if (activity.description && activity.responsible_id && activity.expiration_date && activity.state)
          {
            keys.push(activity);
          }
        });

        this.form.inform.themes[index].actionPlan.activities.splice(0);

        keys.forEach((item, key) => {
            this.form.inform.themes[index].actionPlan.activities.push(item)
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
    showModal(ref) {
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
