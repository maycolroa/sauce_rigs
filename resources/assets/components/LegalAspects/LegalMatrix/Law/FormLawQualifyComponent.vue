<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <loading-block :text="textBlock" v-if="loading || loadingAlternativo"/>
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
          <div class="media-body line-height-condenced ml-3">
              <div class="text-dark">
                <label>Buscar Artículo</label>
                <b-input 
                  placeholder="Buscar..." 
                  type="text"
                  autocomplete="off"
                  v-model="searchArticles"
                  />
              </div>
          </div>
          <br><br>
          <blockquote class="blockquote">
            <p class="mb-0">Filtrar por:</p>
          </blockquote>
          <b-form-row>
            <vue-advanced-select @change="resetReloadShowArticles" class="col-md-4" v-model="filterQualification" :options="filterQualificationOptions" :hide-selected="false" name="filterQualification" label="Calificación" placeholder="Seleccione la Calificación">
                    </vue-advanced-select>
            <vue-advanced-select @change="resetReloadShowArticles" class="col-md-4" v-model="filterRepealed" :options="siNo" :hide-selected="false" name="filterRepealed" label="Derogado" placeholder="Seleccione una opción">
                    </vue-advanced-select>
            <vue-advanced-select @change="resetReloadShowArticles" class="col-md-4" v-model="filterInterests" :multiple="true" :options="filterInterestsOptions" :allowEmpty="true" name="filterInterests" label="Intereses" placeholder="Seleccione los intereses">
                    </vue-advanced-select>
            <label><strong>IMPORTANTE: Si tiene un filtro aplicado sobre los artículos, se aplicará la evaluación solo en los artículos filtrados.</strong></label>
          </b-form-row>
          <blockquote class="blockquote text-center">
            <p class="mb-0">Artículos de la norma | <b-btn v-if="isEdit" variant="primary" size="sm" @click="$refs.modalQualificationAll.show()" ><span class="ion ion-md-clipboard"></span> Evaluar todos </b-btn></p>

            <b-modal ref="modalQualificationAll" :hideFooter="true" id="modals-qualification-all" class="modal-top" size="lg">
              <div slot="modal-title">
                Evaluar todos los artículos de esta ley...
              </div>

              <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                <b-form-row>
                    <vue-radio v-if="auth.hasRole['Superadmin']" :disabled="viewOnly" class="col-md-12" v-model="hide" :options="siNoRadio" name="hide" label="¿Desea ocultar todos los artículos?">
                      </vue-radio>
                  </b-form-row>
                <b-form-row>
                  <vue-advanced-select ref="qualificationAll" :disabled="viewOnly" class="col-md-6" v-model="fulfillment_value_id" :multiple="false" :options="qualifications" name="fulfillment_value_id_all" label="Evaluación" @selectedName="updateQualifyAll"/>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="responsible" label="Responsable" type="text" name="responsible" placeholder="Responsable"/>
                </b-form-row>
                <b-form-row>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="workplace" label="Centro de trabajo" type="text" name="workplace" placeholder="Centro de trabajo"/>
                  <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="observations" label="Observaciones" name="observations" placeholder="Observaciones" rows="3"/>
                </b-form-row>
                <b-form-row>
                  <vue-file-simple v-if="(fulfillment_value_id && fulfillment_value_id != 3) && (fulfillment_value_id && fulfillment_value_id != 5)" :disabled="viewOnly" class="col-md-12" accept=".pdf" v-model="file_masive" label="Archivo (*.pdf)" name="file_masive" :error="form.errorsFor('file_masive')" placeholder="Seleccione un archivo"/>
                </b-form-row>
                <b-form-row>
                    <vue-radio v-if="fulfillment_value_id == 3 || fulfillment_value_id == 5" :disabled="viewOnly" class="col-md-12" v-model="showActionPlanMasive" :options="siNoRadio" name="showActionPlanMasive" label="¿Desea agregar plan de acción?">
                      </vue-radio>
                  </b-form-row>

                <!-- NO CUMPLE -->               
                  <b-card v-if="showActionPlanMasive == 'SI'" ref="modalPlanMasive" :hideFooter="true" id="modals-default-masive" class="modal-top" size="lg">
                    <div slot="modal-title">
                      Plan de acción <span class="font-weight-light">Evaluar Normas</span><br>
                      <small class="text-muted">Crea planes de acción para tu justificación.</small>
                    </div>

                    <b-card bg-variant="transparent" title="" class="mb-3 box-shadow-none">
                      <action-plan-component
                        :is-edit="!viewOnly"
                        :view-only="viewOnly"
                        :form="form"
                        :action-plan-states="actionPlanStates"
                        v-model="actionPlanMasive"
                        :action-plan="actionPlanMasive"/>
                    </b-card>
                  </b-card>
              </b-card>
              <br>
              <div class="row float-right pt-12 pr-12y">
                <b-btn variant="default" @click="$refs.modalQualificationAll.hide()" :disabled="loading">Cerrar</b-btn>&nbsp;&nbsp;
                <b-btn variant="primary" @click="saveAllArticlesConfirm()" :disabled="loading">Guardar</b-btn>
              </div>
            </b-modal>

            <b-modal ref="modalConfirmationHideLaw" :hideFooter="true" id="modals-qualification-all" class="modal-top" size="lg">
              <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                <b-form-row>
                    <center><p style="text-align: center;">¿Esta seguro de ocultar todos los articulos de la norma? Esto la ocultara al completo</p></center>
                  </b-form-row>
              </b-card>
              <br>
              <div class="row float-right pt-12 pr-12y">
                <b-btn variant="default" @click="$refs.modalConfirmationHideLaw.hide()" :disabled="loading">Cerrar</b-btn>&nbsp;&nbsp;
                <b-btn variant="primary" @click="saveAllArticles()" :disabled="loading">Guardar</b-btn>
              </div>
            </b-modal>

            <b-modal ref="modalConfirmationShowLaw" :hideFooter="true" id="modals-qualification-all" class="modal-top" size="lg">
              <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                <b-form-row>
                    <center><p style="text-align: center;">¿Esta seguro de mostrar todos los articulos de la norma? Esto la mostrara en el listado para evaluar</p></center>
                  </b-form-row>
              </b-card>
              <br>
              <div class="row float-right pt-12 pr-12y">
                <b-btn variant="default" @click="$refs.modalConfirmationShowLaw.hide()" :disabled="loading">Cerrar</b-btn>&nbsp;&nbsp;
                <b-btn variant="primary" @click="saveAllArticles()" :disabled="loading">Guardar</b-btn>
              </div>
            </b-modal>
          </blockquote>

          <b-form-row>
            <template v-for="(article, index) in form.articles">
              <b-card no-body class="mb-2 border-secondary" :key="article.key" style="width: 100%;" v-if="article.show_article">
                <b-card-header class="bg-secondary">
                  <b-row>
                    <b-col cols="10" class="d-flex justify-content-between"> 
                      {{ article.description.length > 200 ? `${article.description.substring(0, 200)}...` : article.description }}
                    </b-col>
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
                    <vue-radio v-if="auth.hasRole['Superadmin']" :disabled="viewOnly" :checked="form.articles[index].hide" class="col-md-12" v-model="form.articles[index].hide" @input="saveArticleQualification(index)" :options="siNoRadio" :name="`hide${index}`" :error="form.errorsFor(`articles.${index}.hide`)" label="¿Desea ocultar el artículo?">
                      </vue-radio>
                  </b-form-row>

                  <b-form-row>
                    <vue-advanced-select @selectedName="updateQualify($event, index)" :disabled="viewOnly" class="col-md-6" v-model="article.fulfillment_value_id" :options="qualifications" :allow-empty="false" :name="`fulfillment_value_id_${article.id}`" label="Evaluación"/>

                    <vue-input @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-6" v-model="article.responsible" label="Responsable" name="responsible" :error="form.errorsFor('responsible')" placeholder="Responsable"/>
                  </b-form-row>
                  <b-form-row>
                    <vue-input @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-6" v-model="article.workplace" label="Centro de trabajo" type="text" name="workplace" placeholder="Centro de trabajo"/>
                    <vue-textarea @onBlur="saveArticleQualification(index)" :disabled="viewOnly" class="col-md-6" v-model="article.observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor(`observations`)" rows="3"/>
                  </b-form-row>

                  <b-form-row> 
                    <vue-file-simple v-if="article.qualify && article.qualify != 'No cumple' && article.qualify && article.qualify != 'Parcial'" :help-text="article.old_file ? `Para descargar el archivo actual, haga click <a href='/legalAspects/legalMatrix/law/downloadArticleQualify/${article.qualification_id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-6" @input="saveArticleQualification(index)" accept=".pdf" v-model="article.file" label="Archivo (*.pdf)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"/>

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

          <blockquote class="blockquote text-center">
            <p class="mb-0">Mostrando {{ currentShow }} de {{ totalShow }}</p>
          </blockquote>

          <div class="col-md-12">
            <center>
              <b-btn variant="primary"
                v-if="currentShow < totalShow"
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
import Form from "@/utils/Form.js";
import InformationGeneral from "./InformationGeneral.vue";
import ShowHistoryQualify from "./ShowHistoryQualifyComponent.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    InformationGeneral,
    ShowHistoryQualify,
    VueAdvancedSelect,
    VueInput,
    VueTextarea,
    VueFileSimple,
    ActionPlanComponent,
    VueRadio
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
          articles: [],
          url: ''
        };
      }
    }
  },
  watch: {
    currentAxios() {
      if (this.eventSaveAll)
      {
        if (this.currentAxios == this.totalAxios)
        {
          this.textBlock = 'Recargando Página...';
          setTimeout(() => {
            location.reload();
          }, 3000);
        }
      }
    },
    'searchArticles' : function() {
      console.log(this.searchArticles)
      this.searchOptions();
    }
  },
  computed: {
  },
   data() {
    return {
      loading: true,
      form: Form.makeFrom(this.law, this.method, false, false),
      limitShowArticles: 10,
      currentShow: 0,
      totalShow: 0,
      idHistory: '',
      filterQualificationOptions: [],
      filterQualification: '',
      filterRepealed: '',
      filterInterests: '',
      activateEvent: false,
      fulfillment_value_id: '',
      responsible: '',
      observations: '',
      workplace: '',
      hide: '',
      showActionPlanMasive: '',
      actionPlanMasive: {
        activities: [],
        activitiesRemoved: []
      },
      qualifyName: '',
      file_masive: '',
      loadingAlternativo: false,
      totalAxios: 0,
      currentAxios: 0,
      eventSaveAll: false,
      textBlock: 'Cargando...',
      siNoRadio: [
          {text: 'SI', value: 'SI'},
          {text: 'NO', value: 'NO'}
      ],
      searchArticles: ''
    };
  },
  mounted() {
    setTimeout(() => {
      this.$nextTick(() => {
        this.activateEvent = true;
      });
    }, 5000)
  },
  created() {
    this.builderFilterQualificationOptions();
    this.reloadShowArticles();
  },
  methods: {
    resetReloadShowArticles() {
      this.limitShowArticles = 10;
      this.reloadShowArticles();
    },
    reloadShowArticles() {
      this.loading = true;
      this.currentShow = 0;
      this.totalShow = 0;

      _.forIn(this.form.articles, (article, key) => {
        let show = true;
        article.show_article_real = false;

        if (this.currentShow == this.limitShowArticles)
        {
          show = false;

          if (!this.checkFilter(article, 'filterQualification', 'qualify') && !this.checkFilter(article, 'filterRepealed', 'repealed') && this.checkFilterInterest(article))
          {
            this.totalShow++;
            article.show_article_real = true;
          }
        }
        else if (this.checkFilter(article, 'filterQualification', 'qualify') || this.checkFilter(article, 'filterRepealed', 'repealed') || !this.checkFilterInterest(article))
        {
          show = false;     
        }
        else if (article.hide == 'SI' && !auth.hasRole['Superadmin'])
        {
          show = false;
        }
        else
        {
          this.totalShow++;
          article.show_article_real = true;
        }

        article.show_article = show;
        article.key = !show ? new Date().getTime() + Math.round(Math.random() * 10000) : article.key;
        this.currentShow += show ? 1 : 0;
      });

      setTimeout(() => {
        this.$nextTick(() => {
          this.loading = false;
        });
      }, 5000)
    },
    checkFilter(article, key, propety) {
      return this[key] != '' && article[propety].toLowerCase().indexOf(this[key].toLowerCase()) !== 0
    },
    checkFilterInterest(article) {
      let show = true;

      if (this.filterInterests.length > 0)
      {
        show = false

        for (let i = 0; i < this.filterInterests.length; i++)
        {
          if (article.interests_list.includes(this.filterInterests[i].name))
          {
            show = true
            break;
          }
        }
      }

      return show;
    },
    showMore() {
      this.limitShowArticles += 10;
      this.reloadShowArticles();
    },
    showModalHistory(id) {
      this.idHistory = id
    },
    showModal(ref) {
			this.$refs[ref][0].show();
		},
		hideModal(ref) {
			this.$refs[ref][0].hide();
		},
    closeModalHistory() {
      this.idHistory = ''
    },
    searchOptions() {
      this.currentShow = 0;
      this.totalShow = 0;

      _.forIn(this.form.articles, (article) => {
        let show = true;
        article.show_article_real = false;

        if (this.currentShow == this.limitShowArticles)
        {
          show = false;

          if (!article.description.toLowerCase().includes(this.searchArticles.toLowerCase()))
          {
            this.totalShow++;
            article.show_article_real = true;
          }
        }
        else if (!article.description.toLowerCase().includes(this.searchArticles.toLowerCase()))
        {
            show = false;
        } 
        else
        {
          this.totalShow++;
          article.show_article_real = true;
        }

        article.show_article = show;
        article.key = !show ? new Date().getTime() + Math.round(Math.random() * 10000) : article.key;
        this.currentShow += show ? 1 : 0;
      });

      setTimeout(() => {
        this.$nextTick(() => {
          this.loading = false;
        });
      }, 2000)
    },
    builderFilterQualificationOptions() {
      this.filterQualificationOptions.splice(0);
      this.filterQualificationOptions = this.form.articles
      .map((f) => {
        if (f.qualify)
          return f.qualify
      })
      .filter((value, index, self) => value && self.indexOf(value) === index)
      .map((f) => {
        return {"name": f, "value": f}
      })
    },
    updateQualify(event, index) {
      if (event) {
        this.form.articles[index].qualify = event;
        this.saveArticleQualification(index);
        this.builderFilterQualificationOptions();
      }
    },
    deleteFile(index) {
      this.form.articles[index].file = null;
      this.saveArticleQualification(index);
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
    updateQualifyAll(value) {
      this.qualifyName = value;
    },
    saveAllArticlesConfirm()
    {
      if (this.hide == 'SI')
      {
        this.$refs.modalConfirmationHideLaw.show()
      }
      else if (this.hide == 'NO')
      {
        this.$refs.modalConfirmationShowLaw.show()
      }
      else
      {
        this.saveAllArticles()
      }
    },
    saveAllArticles()
    {
      if (this.fulfillment_value_id != '')
      {
        this.textBlock = 'Guardando...';
        this.loadingAlternativo = true;        
        let data = new FormData();
        let ids = [];

        _.forIn(this.form.articles, (article, key) => {
          if (article.show_article_real)
            {
              ids.push(article.qualification_id);
            }       
        });

        data.append('id', ids);
        data.append('observations', this.observations ? this.observations : '');
        data.append('responsible', this.responsible ? this.responsible : '');
        data.append('workplace', this.workplace ? this.workplace : '');
        data.append('hide', this.hide ? this.hide : 'NO');
        data.append('fulfillment_value_id', this.fulfillment_value_id);
        data.append('actionPlan', JSON.stringify(this.actionPlanMasive));
        data.append('file', this.file_masive);

        this.form
          .submit('/legalAspects/legalMatrix/law/saveArticlesQualificationAlls', false, data)
          .then(response => {
            this.textBlock = 'Recargando Página...';
            setTimeout(() => {
              location.reload();
            }, 3000);
          })
          .catch(error => {
            location.reload();
          });
      }
      else
      {
        Alerts.error('Error', 'El campo calificación es requerido');
      }
    },
    saveAllArticles11()
    {
      if (this.fulfillment_value_id != '')
      {
        this.textBlock = 'Guardando...';
        this.loadingAlternativo = true;
        this.eventSaveAll = true;
        this.totalAxios = 0;
        this.currentAxios = 0;

        _.forIn(this.form.articles, (article, key) => { 
          if (article.show_article_real)
            this.totalAxios++;
        });

        _.forIn(this.form.articles, (article, key) => {
            
            if (article.show_article_real)
            {
              article.fulfillment_value_id = this.fulfillment_value_id;

              if (this.observations)
                article.observations = this.observations;

              if (this.responsible)
                article.responsible = this.responsible;
                
              article.qualify = this.qualifyName;
              
              this.saveArticleQualification(key);
            }
        });
      }
      else
      {
        Alerts.error('Error', 'El campo calificación es requerido');
      }
    },
    saveArticleQualification(index)
    {
      if (this.activateEvent && !this.loading)
      {
        let article = this.form.articles[index]

        /*if (article.fulfillment_value_id != 3 && article.fulfillment_value_id != 5)//No cumple
        {
          if (typeof article.actionPlan !== 'undefined')
          {
            article.actionPlan.activities.forEach((action, index2) => {
              if (action.id != '')
                article.actionPlan.activitiesRemoved.push(action)
            });

            article.actionPlan.activities = [];
          }
        }*/
        
        let data = new FormData();
        data.append('id', article.id);
        data.append('qualification_id', article.qualification_id);
        data.append('observations', article.observations == null ? '' : article.observations);
        data.append('responsible', article.responsible == null ? '' : article.responsible);
        data.append('workplace', article.workplace == null ? '' : article.workplace);
        data.append('hide', article.hide == null ? '' : article.hide);
        
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
            //this.currentAxios++;
          })
          .catch(error => {
            //this.currentAxios++;
            //this.loading = false;
          });

        if (article.hide == 'SI')
        {
          this.resetReloadShowArticles();
        }
      }
    }
  }
};
</script>
