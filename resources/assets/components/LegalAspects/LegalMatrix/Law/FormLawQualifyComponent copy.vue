<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between"> Información General </b-col>
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
            <b-col cols="11" class="d-flex justify-content-between"> Artículos </b-col>
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
            <blockquote class="blockquote">
              <p class="mb-0">Filtrar por:</p>
            </blockquote>
            <b-form-row>
              <vue-advanced-select class="col-md-4" v-model="filterQualification" :multiple="false" :options="filterQualificationOptions" :hide-selected="false" name="filterQualification" label="Calificación" placeholder="Seleccione la Calificación">
                      </vue-advanced-select>
              <vue-advanced-select class="col-md-4" v-model="filterRepealed" :multiple="false" :options="siNo" :hide-selected="false" name="filterRepealed" label="Derogado" placeholder="Seleccione una opción">
                      </vue-advanced-select>
              <vue-advanced-select class="col-md-4" v-model="filterInterests" :multiple="true" :options="filterInterestsOptions" :allowEmpty="true" name="filterInterests" label="Intereses" placeholder="Seleccione los intereses">
                      </vue-advanced-select>
            </b-form-row>
            <blockquote class="blockquote text-center">
              <p class="mb-0">Artículos de la norma | <b-btn v-if="isEdit" variant="primary" size="sm" @click="$refs.modalQualificationAll.show()" ><span class="ion ion-md-clipboard"></span> Evaluar todos </b-btn></p>

              <b-modal ref="modalQualificationAll" :hideFooter="true" id="modals-qualification-all" class="modal-top" size="lg">
                <div slot="modal-title">
                  Evaluar todos los artículos de esta ley...
                </div>

                <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                  <b-form-row>
                    <vue-advanced-select ref="qualificationAll" :disabled="viewOnly" class="col-md-6" v-model="fulfillment_value_id" :multiple="false" :options="qualifications" name="fulfillment_value_id" label="Evaluación" @selectedName="updateQualifyAll"></vue-advanced-select>
                    <vue-input :disabled="viewOnly" class="col-md-6" v-model="responsible" label="Responsable" type="text" name="responsible" placeholder="Responsable"></vue-input>
                  </b-form-row>
                  <b-form-row>
                    <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="observations" label="Observaciones" name="observations" placeholder="Observaciones" rows="3"></vue-textarea>
                  </b-form-row>
                </b-card>
                <br>
                <div class="row float-right pt-12 pr-12y">
                  <b-btn variant="default" @click="$refs.modalQualificationAll.hide()" :disabled="loading">Cerrar</b-btn>&nbsp;&nbsp;
                  <b-btn variant="primary" @click="saveAllArticles()" :disabled="loading">Guardar</b-btn>
                </div>
              </b-modal>
            </blockquote>
            <b-form-row>
              <b-form-feedback class="d-block" v-if="form.errorsFor(`articles`)" style="padding-bottom: 10px;">
                {{ form.errorsFor(`articles`) }}
              </b-form-feedback>
                <template v-for="(article, index) in articlesToDisplay">
                  <b-card no-body class="mb-2 border-secondary" :key="article.key" style="width: 100%;" v-show="showArticle(article)">
                    <b-card-header class="bg-secondary">
                      <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> {{ article.description ? (article.description.length > 200 ? `${article.description.substring(0, 200)}...` : article.description) : `Nuevo Artìculo ${index + 1}` }}</b-col>
                        <b-col cols="2">
                          <div class="float-right">
                            <b-button-group>
                              <b-btn @click="showModalHistory(article.qualification_id)" 
                                size="sm" 
                                variant="secondary icon-btn borderless"
                                v-b-tooltip.top title="Ver historial de cambios">
                                  <span class="ion ion-md-eye"></span>
                              </b-btn>
                              <b-btn @click="showModal(`modalArticle${index}`)"
                                size="sm" 
                                variant="secondary icon-btn borderless"
                                v-b-tooltip.top title="Ver artículo completo">
                                  <span class="fas fa-book-open"></span>
                              </b-btn>
                            </b-button-group>

                            <b-modal :ref="`modalArticle${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg">
                              <div slot="modal-title">
                                Última modificación: <span class="font-weight-light">{{ article.updated_at }}</span><br>
                                Derogado: <span class="font-weight-light">{{ article.repealed }}</span>
                                <b-row style="padding-top: 20px;">
                                  <b-col>
                                    <div><b>Intereses:</b> {{ article.interests_string }} </div>
                                  </b-col>
                                </b-row>
                              </div>

                              <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                <b-row>
                                  <b-col>
                                    <div>{{ article.description }}<br><br></div>
                                  </b-col>
                                </b-row>
                                
                                <b-form-row>
                                  <vue-advanced-select :ref="`qualification2${index}`" @input="syncArticleFull(index)" @selectedName="updateQualify($event, index)" :disabled="viewOnly" class="col-md-6" v-model="article.fulfillment_value_id" :multiple="false" :options="qualifications" name="fulfillment_value_id" label="Evaluación"></vue-advanced-select>

                                  <vue-input @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-6" v-model="article.responsible" label="Responsable" type="text" name="responsible" :error="form.errorsFor('responsible')" placeholder="Responsable"></vue-input>
                                </b-form-row>
                                <b-form-row>
                                  <vue-textarea @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-12" v-model="article.observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor(`observations`)" rows="3"></vue-textarea>
                                </b-form-row>

                                <b-form-row> 
                                  <vue-file-simple v-if="article.qualify && article.qualify != 'No cumple' && article.qualify && article.qualify != 'Parcial'" :help-text="article.old_file ? `Para descargar el archivo actual, haga click <a href='/legalAspects/legalMatrix/law/downloadArticleQualify/${article.qualification_id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-6" @input="saveArticleQualification(index)" accept=".pdf" v-model="article.file" label="Archivo (*.pdf)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>

                                  <div style="padding-top: 25px;" v-if="isEdit">
                                    <b-btn v-if="article.qualify && article.qualify != 'No cumple' && article.qualify != 'Parcial' && article.file" @click="deleteFile(index)" variant="primary"><span class="ion ion-md-close-circle"></span> Eliminar Archivo</b-btn>
                                  </div>

                                  <!-- NO CUMPLE -->
                                  <b-btn v-if="article.qualify == 'No cumple' || article.qualify == 'Parcial'" @click="showModal(`modalPlan${index}`)" variant="primary"><span class="lnr lnr-bookmark"></span> Plan de acción</b-btn>
                                </b-form-row>
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
                      <b-card-body>
                        <b-form-row>
                          <div class="text-center col-md-6">
                            <b>Última modificación: </b><span class="font-weight-light">{{ article.updated_at }}</span>
                          </div>
                          <div class="text-center col-md-6">
                            <b>Derogado: </b><span class="font-weight-light">{{ article.repealed }}</span>
                          </div>
                        </b-form-row>
                        <b-form-row>
                          <div class="text-center col-md-12">
                            <b>Intereses: </b><span class="font-weight-light">{{ article.interests_string }}</span>
                          </div>
                        </b-form-row>
                        <hr class="border-light container-m--x mt-0 mb-4">
                        <b-form-row>
                          <vue-advanced-select :ref="`qualification${index}`" @input="saveArticleQualification(index)" @selectedName="updateQualify($event, index)" :disabled="viewOnly" class="col-md-6" v-model="article.fulfillment_value_id" :multiple="false" :options="qualifications" :allow-empty="false" name="fulfillment_value_id" label="Evaluación"></vue-advanced-select>

                          <vue-input @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-6" v-model="article.responsible" label="Responsable" type="text" name="responsible" :error="form.errorsFor('responsible')" placeholder="Responsable"></vue-input>
                      </b-form-row>
                      <b-form-row>
                          <vue-textarea @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-12" v-model="article.observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor(`observations`)" rows="3"></vue-textarea>
                      </b-form-row>
                      <b-form-row> 
                        <vue-file-simple v-if="article.qualify && article.qualify != 'No cumple' && article.qualify && article.qualify != 'Parcial'" :help-text="article.old_file ? `Para descargar el archivo actual, haga click <a href='/legalAspects/legalMatrix/law/downloadArticleQualify/${article.qualification_id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-6" @input="saveArticleQualification(index)" accept=".pdf" v-model="article.file" label="Archivo (*.pdf)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>

                        <div style="padding-top: 25px;" v-if="isEdit">
                          <b-btn v-if="article.qualify && article.qualify != 'No cumple' && article.qualify != 'Parcial' && article.file" @click="deleteFile(index)" variant="primary"><span class="ion ion-md-close-circle"></span> Eliminar Archivo</b-btn>
                        </div>

                          <!-- NO CUMPLE -->
                          <b-btn v-if="article.qualify == 'No cumple' || article.qualify == 'Parcial'" @click="showModal(`modalPlan${index}`)" variant="primary"><span class="lnr lnr-bookmark"></span> Plan de acción</b-btn>

                          <b-modal v-if="article.qualify == 'No cumple' || article.qualify == 'Parcial'" :ref="`modalPlan${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg" @hidden="saveArticleQualification(index)">
                            <div slot="modal-title">
                              Plan de acción <span class="font-weight-light">Evaluar Normas</span><br>
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

                          <div v-if="existError(`articles.${index}.`)">
                              <b-form-feedback class="d-block" style="padding-bottom: 10px; padding-left: 10px;">
                                Este artículo contiene errores en sus datos de planes de acción
                              </b-form-feedback>
                          </div>
                        </b-form-row>
                      </b-card-body>
                  </b-card>
                </template>
            </b-form-row>
          </div>
          <div class="col-md-12">
            <center>
              <b-btn variant="primary"
                v-if="(articlesToDisplay.length > 0) && (form.articles.length > limitShowArticles)"
                @click.prevent="showMore" 
                :disabled="loading">Ver mas</b-btn>
            </center>
          </div>
        </b-card-body>
      </b-collapse>
    </b-card>

    <show-history-qualify
      v-if="idHistory"
      :id="idHistory"
      @close-modal-history="closeModalHistory"
    />

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
import ShowHistoryQualify from "./ShowHistoryQualifyComponent.vue";

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
    ActionPlanComponent,
    ShowHistoryQualify
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
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    filterInterestsOptions: {
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
          system_apply_id: '',
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
      this.form = Form.makeFrom(this.law, this.method, false, false);

      setTimeout(() => {
          this.ready = true
      }, 5000)
    }
  },
  computed: {
    filterQualificationOptions() {

      let options = []

      if (this.ready)
      {
        options = this.form.articles
        .map((f) => {
          if (f.qualify)
            return f.qualify
        })
        .filter((value, index, self) => value && self.indexOf(value) === index)
        .map((f) => {
          return {"name": f, "value": f}
        })

        if (options.length == 0)
          return []
      }

      return options;
    },
    articlesToDisplay() {

      if (this.form.articles)
      {
        let range = 0;
        let count = 0;
        //this.showArticle
        for (let i = 0; i < this.form.articles.length; i++)
        {
          count += this.showArticle(this.form.articles[i]) ? 1 : 0;
          range++;
          if (count == this.limitShowArticles)
            break;
        }
        this.limitShowArticles = range;
        return this.form.articles.slice(0, range);
        //return this.form.articles.slice(0, this.limitShowArticles);
      }
      
      return []
    }
  },
   data() {
    return {
        ready: false,
        loading: this.isEdit,
        form: Form.makeFrom(this.law, this.method),
        filterQualification: '',
        filterRepealed: '',
        filterInterests: [],
        fulfillment_value_id: '',
        responsible: '',
        observations: '',
        qualifyName: '',
        idHistory: '',
        limitShowArticles: 20
    };
  },
  methods: {
    showMore() {
      this.ready = false;
      this.loading = true;

      this.limitShowArticles += 20;

      setTimeout(() => {
          this.ready = true;
          this.loading = false;
      }, 5000)
    },
    showModalHistory(id) {
      this.idHistory = id
    },
    closeModalHistory() {
      this.idHistory = ''
    },
    showModal(ref) {
			this.$refs[ref][0].show()
		},
		hideModal(ref) {
			this.$refs[ref][0].hide()
		},
    updateQualify(event, index) {
      this.form.articles[index].qualify = event
    },
    updateQualifyAll(value) {
      this.qualifyName = value
    },
    showArticle(article) {
      let resultQualification = true
      let resultRepealed = true
      let resultInterests = true

      if (this.filterQualification != '')
      {
        resultQualification = article.qualify.toLowerCase().indexOf(this.filterQualification.toLowerCase()) !== -1 ? true : false
      }

      if (this.filterRepealed != '')
      {
        resultRepealed = article.repealed.toLowerCase().indexOf(this.filterRepealed.toLowerCase()) !== -1 ? true : false
      }

      if (this.filterInterests.length > 0)
      {
        let find = false

        for (let i = 0; i < this.filterInterests.length; i++)
        {
          if (article.interests_list.includes(this.filterInterests[i].name))
          {
            find = true
            break;
          }
        }

        resultInterests = find ? true : false
      }

      return resultQualification && resultRepealed && resultInterests
    },
    existError(index) {
			let keys = Object.keys(this.form.errors.errors)
			let result = false

			if (keys.length > 0)
			{
				for (let i = 0; i < keys.length; i++)
				{
					if (keys[i].indexOf(index) > -1)
					{
						result = true
						break
					}
				}
			}

			return result
    },
    deleteFile(index)
    {
      this.form.articles[index].file = null;
      this.saveArticleQualification(index) 
    },
    saveAllArticles()
    {
      if (this.fulfillment_value_id != '')
      {
        _.forIn(this.form.articles, (article, key) => {
            
            if (this.showArticle(article))
            {
              article.fulfillment_value_id = this.fulfillment_value_id

              if (this.observations)
                article.observations = this.observations

              if (this.responsible)
                article.responsible = this.responsible
                
              article.qualify = this.qualifyName

              this.$nextTick(() => {
                this.$refs[`qualification${key}`][0].refreshData()
                this.$refs[`qualification2${key}`][0].refreshData()
              })

              //this.saveArticleQualification(key)  
            }
        });

        this.$refs.modalQualificationAll.hide()
        this.fulfillment_value_id = ''
        this.observations = ''
        this.responsible = ''
        this.qualifyName = ''
        this.$refs.qualificationAll.cleanData()
      }
      else
      {
        Alerts.error('Error', 'El campo calificación es requerido');
      }
    },
    syncArticleFull(index)
    {
      this.$nextTick(() => {
        this.$refs[`qualification${index}`][0].refreshData()
      })
    },
    saveArticleQualification(index)
    {
      if (this.ready && this.isEdit)
      {
        this.loading = true;
        let article = this.form.articles[index]

        if (article.fulfillment_value_id != 3 && article.fulfillment_value_id != 5)//No cumple
        {
          if (typeof article.actionPlan !== 'undefined')
          {
            article.actionPlan.activities.forEach((action, index2) => {
              if (action.id != '')
                article.actionPlan.activitiesRemoved.push(action)
            });

            article.actionPlan.activities = [];
          }
        }
        
        let data = new FormData();
        data.append('id', article.id);
        data.append('qualification_id', article.qualification_id);
        data.append('observations', article.observations == null ? '' : article.observations);
        data.append('responsible', article.responsible == null ? '' : article.responsible);
        data.append('fulfillment_value_id', article.fulfillment_value_id == null ? '' : article.fulfillment_value_id);
        data.append('file', article.file == null ? '' : article.file);
        data.append('actionPlan', JSON.stringify(article.actionPlan));
        data.append(`articles[${index}]`, JSON.stringify({ actionPlan: article.actionPlan }));

        this.form.resetError()
        this.form
          .submit('/legalAspects/legalMatrix/law/saveArticlesQualification', false, data)
          .then(response => {
            _.forIn(response.data.data, (value, key) => {
              article[key] = value
            })

            this.loading = false;
            
          })
          .catch(error => {
            this.loading = false;
          });
      }
    }
  }
};
</script>
