<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between text-white"> Información General </b-col>
          <b-col cols="1">
            <div class="float-right">
              <b-button-group>
                <b-btn href="javascript:void(0)" v-b-toggle="'accordion-general'" variant="link">
                  <span class="collapse-icon"></span>
                </b-btn>
              </b-button-group>
            </div>
          </b-col>
        </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-general`" visible :accordion="`accordion-master`">
        <b-card-body>
          <information-general
          :law="law"/>
        </b-card-body>
      </b-collapse>
    </b-card>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
          <b-row>
            <b-col cols="11" class="d-flex justify-content-between text-white"> Artículos </b-col>
            <b-col cols="1">
                <div class="float-right">
                  <b-button-group>
                    <b-btn href="javascript:void(0)" v-b-toggle="'accordion-articles'" variant="link">
                      <span class="collapse-icon"></span>
                    </b-btn>
                  </b-button-group>
                </div>
            </b-col>
          </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-articles`" visible :accordion="`accordion-master`">
        <b-card-body>
          <div class="col-md-12">
            <blockquote class="blockquote text-center">
              <p class="mb-0">Artículos de la norma</p>
            </blockquote>
            <b-form-row>
              <b-form-feedback class="d-block" v-if="form.errorsFor(`articles`)" style="padding-bottom: 10px;">
                {{ form.errorsFor(`articles`) }}
              </b-form-feedback>
              <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                <template v-for="(article, index) in form.articles">
                  <b-card no-body class="mb-2 border-secondary" :key="article.key" style="width: 100%;">
                    <b-card-header class="bg-secondary">
                      <b-row>
                        <b-col cols="10" class="d-flex justify-content-between text-white"> {{ form.articles[index].description ? (form.articles[index].description.length > 200 ? `${form.articles[index].description.substring(0, 200)}...` : form.articles[index].description) : `Nuevo Artìculo ${index + 1}` }}</b-col>
                        <b-col cols="2">
                          <div class="float-right">
                            <b-button-group>
                              <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + article.key+'-1'" variant="link">
                                <span class="collapse-icon"></span>
                              </b-btn>
                              <b-btn @click="showModal(`modalArticle${index}`)" 
                                v-if="!viewOnly"
                                size="sm" 
                                variant="secondary icon-btn borderless"
                                v-b-tooltip.top title="Ver artículo completo">
                                  <span class="ion ion-md-eye"></span>
                              </b-btn>
                            </b-button-group>

                            <b-modal :ref="`modalArticle${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg">
                              <div slot="modal-title">
                                Última modificación: <span class="font-weight-light">{{ article.updated_at }}</span><br>
                                Derogada: <span class="font-weight-light">{{ article.repelead }}</span>
                              </div>

                              <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                <b-row>
                                  <b-col>
                                    <div>{{ article.description }}<br><br></div>
                                  </b-col>
                                </b-row>
                                <b-row style="padding-top: 20px;">
                                  <b-col>
                                    <div><b>Intereses:</b> {{ article.interests_string }} </div>
                                  </b-col>
                                </b-row>
                              </b-card>
                              <br>
                              <div class="row float-right pt-12 pr-12y">
                                <b-btn variant="primary" @click="hideModal(`modalArticle${index}`)">Cerrar</b-btn>
                              </div>
                            </b-modal>
                          </div>
                        </b-col>
                      </b-row>
                    </b-card-header>
                    <b-collapse :id="`accordion${article.key}-1`" :visible="!isEdit && !viewOnly" :accordion="`accordion-123`">
                      <b-card-body>
                        <b-form-row>
                          <div class="text-center col-md-6">
                            <b>Última modificación: </b><span class="font-weight-light">{{ article.updated_at }}</span>
                          </div>
                          <div class="text-center col-md-6">
                            <b>Derogada: </b><span class="font-weight-light">{{ article.repelead }}</span>
                          </div>
                        </b-form-row>
                        <b-form-row>
                          <div class="text-center col-md-12">
                            <b>Intereses: </b><span class="font-weight-light">{{ article.interests_string }}</span>
                          </div>
                        </b-form-row>
                        <hr class="border-light container-m--x mt-0 mb-4">
                        <b-form-row>
                          <vue-textarea @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-12" v-model="form.articles[index].observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor(`articles.${index}.observations`)" rows="3"></vue-textarea>
                        </b-form-row>
                        <b-form-row>
                          <vue-input @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-6" v-model="form.articles[index].responsible" label="Responsable" type="text" name="responsible" :error="form.errorsFor('responsible')" placeholder="Responsable"></vue-input>
                       
                        <vue-advanced-select @input="saveArticleQualification(index)" @selectedName="updateQualify($event, index)" :disabled="viewOnly" class="col-md-6" v-model="form.articles[index].fulfillment_value_id" :multiple="false"  :options="qualifications" name="fulfillment_value_id" label="Calificación"></vue-advanced-select>
                      </b-form-row>
                      <b-form-row> 
                        <vue-file-simple v-if="article.qualify && article.qualify != 'No cumple'" :help-text="form.articles[index].old_file ? `Para descargar el archivo actual, haga click <a href='/legalAspects/legalMatrix/law/downloadArticleQualify/${form.articles[index].qualification_id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-6" @input="saveArticleQualification(index)" accept=".pdf" v-model="form.articles[index].file" label="Archivo (*.pdf)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>

                          <!-- NO CUMPLE -->
                          <b-btn v-if="article.qualify == 'No cumple'" @click="showModal(`modalPlan${index}`)" variant="primary"><span class="lnr lnr-bookmark"></span> Plan de acción</b-btn>

                          <b-modal v-if="article.qualify == 'No cumple'" :ref="`modalPlan${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg">
                            <div slot="modal-title">
                              Plan de acción <span class="font-weight-light">Calificar Normas</span><br>
                              <small class="text-muted">Crea planes de acción para tu justificación.</small>
                            </div>

                            <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                              <action-plan-component
                                :is-edit="!viewOnly"
                                :view-only="viewOnly"
                                :form="form"
                                :prefix-index="`articles.${index}.`"
                                :action-plan-states="actionPlanStates"
                                v-model="article.actionPlan"
                                :action-plan="article.actionPlan"/>
                            </b-card>
                            <br>
                            <div class="row float-right pt-12 pr-12y">
                              <b-btn variant="primary" @click="hideModal(`modalPlan${index}`)">Cerrar</b-btn>
                            </div>
                          </b-modal>
                        </b-form-row>
                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </template>
              </perfect-scrollbar>
            </b-form-row>
          </div>
        </b-card-body>
      </b-collapse>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Alerts from '@/utils/Alerts.js';
import InformationGeneral from "./InformationGeneral.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    VueRadio,
    VueFileSimple,
    InformationGeneral,
    ActionPlanComponent
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    qualifications: {
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
    law: {
      default() {
        return {
          name: '',
          law_number: '',
          apply_system: '',
          law_year: '',
          law_type_id: '',
          description: '',
          observations: '',
          risk_aspect_id: '',
          entity_id: '',
          sst_risk_id: '',
          repealed: '',
          file: '',
          articles: []
        };
      }
    }
  },
  watch: {
    law() {
      this.loading = false;
      this.form = Form.makeFrom(this.law, this.method);

      setTimeout(() => {
          this.ready = true
      }, 5000)
    }
  },
   data() {
    return {
        ready: false,
        loading: this.isEdit,
        form: Form.makeFrom(this.law, this.method)
    };
  },
  methods: {
    showModal(ref) {
			this.$refs[ref][0].show()
		},
		hideModal(ref) {
			this.$refs[ref][0].hide()
		},
    updateQualify(event, index) {
      this.form.articles[index].qualify = event
    },
    saveArticleQualification(index)
    {
      if (this.ready)
      {
        let data = new FormData();
        data.append('id', this.form.articles[index].id);
        data.append('qualification_id', this.form.articles[index].qualification_id);
        data.append('observations', this.form.articles[index].observations);
        data.append('responsible', this.form.articles[index].responsible);
        data.append('fulfillment_value_id', this.form.articles[index].fulfillment_value_id);
        data.append('file', this.form.articles[index].file);
        data.append('actionPlan', JSON.stringify(this.form.articles[index].actionPlan));

        axios.post('/legalAspects/legalMatrix/law/saveArticlesQualification', data)
          .then(response => {
            _.forIn(response.data.data, (value, key) => {
              this.form.articles[index][key] = value
            })
              console.log(response.data);
          })
          .catch(error => {
              Alerts.error('Error', 'Hubo un problema guardando la información');
          });
      }
    }
  }
};
</script>
