<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between"> General </b-col>
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
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.law_number" label="Número" type="text" name="law_number" :error="form.errorsFor('law_number')" placeholder="Número"></vue-input>
          </b-form-row>
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.system_apply_id" :error="form.errorsFor('system_apply_id')" :selected-object="form.multiselect_system_apply" name="system_apply_id" label="Sistema que aplica" placeholder="Seleccione el sistema que aplica" :url="systemApplyUrl">
              </vue-ajax-advanced-select>
            <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.law_year" :multiple="false" :options="years" :hide-selected="false" name="law_year" :error="form.errorsFor('law_year')" label="Año" placeholder="Seleccione el año" :searchable="true">
              </vue-advanced-select>
          </b-form-row>
          <b-form-row>
            <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor('description')" rows="2"></vue-textarea>
            <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor('observations')" rows="2"></vue-textarea>
          </b-form-row>
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.law_type_id" :error="form.errorsFor('law_type_id')" :selected-object="form.multiselect_law_type" name="law_type_id" label="Tipo de norma" placeholder="Seleccione el tipo de norma" :url="lawTypeDataUrl">
              </vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.risk_aspect_id" :error="form.errorsFor('risk_aspect_id')" :selected-object="form.multiselect_risk_aspect" name="risk_aspect_id" label="Tema Ambiental" placeholder="Seleccione el tema ambiental" :url="riskAspectDataUrl">
              </vue-ajax-advanced-select>
          </b-form-row>
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.entity_id" :error="form.errorsFor('entity_id')" :selected-object="form.multiselect_entity" name="entity_id" label="Ente" placeholder="Seleccione el ente" :url="entityDataUrl">
              </vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.sst_risk_id" :error="form.errorsFor('sst_risk_id')" :selected-object="form.multiselect_sst_risk" name="sst_risk_id" label="Tema SST" placeholder="Seleccione el tema sst" :url="sstRiskDataUrl">
              </vue-ajax-advanced-select>
          </b-form-row>
          <b-form-row>
            <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.repealed" :multiple="false" :options="repealed" :hide-selected="false" name="repealed" :error="form.errorsFor('repealed')" label="Derogada" placeholder="Seleccione una opciòn">
              </vue-advanced-select>
            <template v-if="isEdit || viewOnly">
						  <vue-file-simple :help-text="form.old_file ? `Para descargar el archivo actual, haga click <a href='/legalAspects/legalMatrix/law/download/${this.$route.params.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-6" v-model="form.file" label="Archivo (*.pdf)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>
            </template>
						<vue-file-simple v-else :disabled="viewOnly" class="col-md-6" v-model="form.file" label="Archivo (*.pdf)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>
          </b-form-row>
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
            <blockquote class="blockquote text-center">
              <p class="mb-0">Artículos de la norma</p>
            </blockquote>
            <b-form-row>
              <div class="col-md-12" v-if="!viewOnly">
                <div class="float-right" style="padding-top: 10px;">
                  <b-btn variant="primary" @click.prevent="addActicle()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Artículo</b-btn>
                </div>
              </div>
            </b-form-row>
            <b-form-row>
              <div class="col-md-12" v-if="!viewOnly && form.articles && form.articles.length > 0">
                <div class="col-md-12">
                  <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="add_interests" name="add_interests" label="Agregar intereses a todos los artículos" placeholder="Seleccione los intereses" :url="urlDataInterests" :multiple="true" :allowEmpty="true">
                    </vue-ajax-advanced-select>
                </div>
                <div class="col-md-12">
                  <center><b-btn variant="primary" @click.prevent="addInterests()" :disabled="loading">Agregar Intereses</b-btn></center>
                </div>
              </div>
            </b-form-row>
            <b-form-row style="padding-top: 15px;">
              <b-form-feedback class="d-block" v-if="form.errorsFor(`articles`)" style="padding-bottom: 10px;">
                {{ form.errorsFor(`articles`) }}
              </b-form-feedback>
              <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                <template v-for="(article, index) in form.articles">
                  <b-card no-body class="mb-2 border-secondary" :key="article.key" style="width: 100%;">
                    <b-card-header class="bg-secondary">
                      <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> {{ form.articles[index].description ? (form.articles[index].description.length > 200 ? `${form.articles[index].description.substring(0, 200)}...` : form.articles[index].description) : `Nuevo Artículo ${index + 1}` }}</b-col>
                        <b-col cols="2">
                          <div class="float-right">
                            <b-button-group>
                              <b-btn @click="showModal(`modalArticle${index}`)" 
                                v-if="viewOnly"
                                size="sm" 
                                variant="secondary icon-btn borderless"
                                v-b-tooltip.top title="Ver historial de cambios">
                                  <span class="ion ion-md-eye"></span>
                              </b-btn>
                              <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + article.key+'-1'" variant="link">
                                <span class="collapse-icon"></span>
                              </b-btn>
                              <b-btn @click.prevent="removeArticle(index)" 
                                v-if="!viewOnly"
                                size="sm" 
                                variant="secondary icon-btn borderless"
                                v-b-tooltip.top title="Eliminar Artículo">
                                  <span class="ion ion-md-close-circle"></span>
                              </b-btn>
                            </b-button-group>

                            <b-modal :ref="`modalArticle${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg">
                              <div slot="modal-title">
                                Historial de cambios realizados
                              </div>

                              <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                <vue-table
                                  configName="legalaspects-lm-article-histories"
                                  :modelId="form.articles[index].id ? form.articles[index].id : -1"
                                  ></vue-table>
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
                          <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.articles[index].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`articles.${index}.description`)" rows="3"></vue-textarea>
                        </b-form-row>
                        <b-form-row>
                          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.articles[index].interests_id" name="interests_id" label="Intereses" placeholder="Seleccione los intereses" :url="urlDataInterests" :multiple="true" :allowEmpty="true" :error="form.errorsFor(`articles.${index}.interests_id`)" :selected-object="form.articles[index].multiselect_interests">
                            </vue-ajax-advanced-select>
                          <vue-radio :disabled="viewOnly" :checked="form.articles[index].repealed" class="col-md-3" v-model="form.articles[index].repealed" :options="siNo" :name="`repealed${index}`" :error="form.errorsFor(`articles.${index}.repealed`)" label="Derogado">
                            </vue-radio>
                          <vue-advanced-select :disabled="viewOnly" class="col-md-3" v-model="form.articles[index].new_sequence" :options="sequenceOptions" name="sequence" label="Secuencia" @change="updateOrder(index)">
                            </vue-advanced-select>
                        </b-form-row>

                      </b-card-body>
                    </b-collapse>
                  </b-card>
                </template>
                <b-form-row v-if="form.articles && form.articles.length > 0">
                  <div class="col-md-12" v-if="!viewOnly">
                    <div class="float-right" style="padding-top: 10px;">
                      <b-btn variant="primary" @click.prevent="addActicle()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Artículo</b-btn>
                    </div>
                  </div>
                </b-form-row>
              </perfect-scrollbar>
            </b-form-row>
          </div>
        </b-card-body>
      </b-collapse>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn @click="submit(false)" :disabled="loading" variant="primary" v-if="!viewOnly">Guardar y continuar</b-btn>&nbsp;&nbsp;
        <b-btn @click="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
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

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    VueRadio,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    lawTypeDataUrl: { type: String, default: "" },
    riskAspectDataUrl: { type: String, default: "" },
    entityDataUrl: { type: String, default: "" },
    sstRiskDataUrl: { type: String, default: "" },
    urlDataInterests: { type: String, default: "" },
    systemApplyUrl: { type: String, default: "" },
    custom: { type: Boolean, default: false },
    years: {
      type: Array,
      default: function() {
        return [];
      }
    },
    repealed: {
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
    law: {
      default() {
        return {
          id:'',
          custom: this.custom,
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
          delete: [],
        };
      }
    }
  },
  watch: {
    law() {
      this.loading = false;
      this.form = Form.makeFrom(this.law, this.method);
    }
  },
  computed: {
    sequenceOptions() {
      let options = []

      for (let index = 1; index <= this.form.articles.length; index++) {
        options.push({name: index, value: index})
      }

      return options
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.law, this.method),
        add_interests: []
    };
  },
  methods: {
    submit(redirect = true) {
      this.loading = true;

      let url = this.url;

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
            this.$router.go(-1);
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
    },
    addActicle() {
        this.form.articles.push({
            key: new Date().getTime() + Math.round(Math.random() * 10000),
            description: '',
            repealed: 'NO',
            sequence: this.form.articles.length + 1,
            new_sequence: this.form.articles.length + 1,
            interests_id: [],
            multiselect_interests: []
        })
    },
    removeArticle(index)
    {
      if (this.form.articles[index].id != undefined)
        this.form.delete.push(this.form.articles[index].id)

      this.form.articles.splice(index, 1)
    },
    updateOrder(index) {
      
      if (this.form.articles[index].new_sequence > this.form.articles[index].sequence)
      {
        for (let i = (index + 1); i < this.form.articles[index].new_sequence; i++) {
          this.form.articles[i].new_sequence -= 1 
          this.form.articles[i].sequence -= 1 
        }
      }
      else if (this.form.articles[index].new_sequence < this.form.articles[index].sequence)
      {
        for (let i = (index - 1); i >= (this.form.articles[index].new_sequence - 1); i--) {
          this.form.articles[i].new_sequence += 1 
          this.form.articles[i].sequence += 1 
        }
      }

      this.form.articles[index].sequence = this.form.articles[index].new_sequence

      this.form.articles.sort((a, b) => (a.sequence > b.sequence) ? 1 : -1)
    },
    showModal(ref) {
			this.$refs[ref][0].show()
		},
		hideModal(ref) {
			this.$refs[ref][0].hide()
    },
    findValue(interests, search) {

      let list = [];
    
      _.forIn(interests, (interest, key) => {
        list.push(interest.value);
      });

      return list.includes(search);
    },
    addInterests()
    {
      if (this.add_interests.length > 0)
      {
        this.loading = true;

        _.forIn(this.form.articles, (article, key) => {

            _.forIn(this.add_interests, (interest2, key3) => {

              if (!this.findValue(article.interests_id, interest2.value))
              {
                article.interests_id.push({
                  name: interest2.name,
                  value: interest2.value
                });
              }
            });

            article.multiselect_interests = article.interests_id;
        });

        this.add_interests.splice(0);

        this.loading = false;
      }
    },
  }
};
</script>