<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <form-wizard ref="wizardFormEvaluation">
        <!-- Allow html in tab title (this template required for the proper styling) -->
        <template slot="step" slot-scope="props">
            <wizard-step :tab="props.tab" @click.native="props.navigateToTab(props.index)" @keyup.enter.native="props.navigateToTab(props.index)" :transition="props.transition" :index="props.index">
            <span slot="title" :class="{'text-danger':props.tab.validationError}" v-html="props.tab.title"></span>
            </wizard-step>
        </template>
        
        <!-- / -->
        <tab-content title="General">
            <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
            <vue-radio :disabled="viewOnly" :checked="form.type" class="col-md-6" v-model="form.type" :options="typesEvaluation" name="type" :error="form.errorsFor('type')" label="Tipo de evaluación">
                </vue-radio>
            </b-form-row>
        </tab-content>

        <tab-content title="Objetivos">
            <b-form-row style="padding-top: 15px; padding-bottom: 15px;">
              <evaluation-types-rating
                :viewOnly="viewOnly"
                :isEdit="isEdit"
                :form="form"
                :typesRating="typesRating"
                v-model="form.types_rating"
              />
            </b-form-row>
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
                                                          <template v-for="(type, indexType) in form.types_rating">
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
                                                            <template v-for="(type, indexType) in form.types_rating">
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

        <template slot="footer" slot-scope="props">
            <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Cancelar" : "Atras"}}</b-btn>
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
import EvaluationTypesRating from './EvaluationTypesRating.vue';

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
    EvaluationTypesRating
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
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
          name: '',
          type: '',
          types_rating: [],
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
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.evaluation, this.method)
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
    }
  }
};
</script>
