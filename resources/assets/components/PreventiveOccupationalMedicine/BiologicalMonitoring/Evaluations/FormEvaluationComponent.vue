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
          </b-form-row>
        </b-card-body>
      </b-collapse>
    </b-card>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
          <b-row>
            <b-col cols="11" class="d-flex justify-content-between"> Etapas </b-col>
            <b-col cols="1">
                <div class="float-right">
                  <b-button-group>
                    <b-btn href="javascript:void(0)" v-b-toggle="'accordion-stages'" variant="link">
                      <span class="collapse-icon"></span>
                    </b-btn>
                  </b-button-group>
                </div>
            </b-col>
          </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-stages`" visible :accordion="`accordion-master`">
        <b-card-body>
          <div class="col-md-12">
            <blockquote class="blockquote text-center">
              <p class="mb-0">Etapas de la evaluación</p>
            </blockquote>
            <b-form-row>
              <div class="col-md-12" v-if="!viewOnly">
                <div class="float-right" style="padding-top: 10px;">
                  <b-btn variant="primary" @click.prevent="addObjetive()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Etapa</b-btn>
                </div>
              </div>
            </b-form-row>
            <b-form-row style="padding-top: 15px;">
              <b-form-feedback class="d-block" v-if="form.errorsFor(`stages`)" style="padding-bottom: 10px;">
                {{ form.errorsFor(`stages`) }}
              </b-form-feedback>
              <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                <template v-for="(stage, index) in form.stages">
                  <b-card no-body class="mb-2 border-secondary" :key="stage.key" style="width: 100%;">
                    <b-card-header class="bg-secondary">
                      <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> {{ form.stages[index].description ? form.stages[index].description : `Nueva Etapa ${index + 1}` }}</b-col>
                        <b-col cols="2">
                          <div class="float-right">
                            <b-button-group>
                              <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + stage.key+'-1'" variant="link">
                                <span class="collapse-icon"></span>
                              </b-btn>
                              <b-btn @click.prevent="removeObjetive(index)" 
                                v-if="!viewOnly"
                                size="sm" 
                                variant="secondary icon-btn borderless"
                                v-b-tooltip.top title="Eliminar Etapa">
                                  <span class="ion ion-md-close-circle"></span>
                              </b-btn>
                            </b-button-group>
                          </div>
                        </b-col>
                      </b-row>
                    </b-card-header>
                    <b-collapse :id="`accordion${stage.key}-1`" visible :accordion="`accordion-123`">
                      <b-card-body>
                        <b-form-row>
                          <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.stages[index].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`stages.${index}.description`)" rows="1"></vue-textarea>
                        </b-form-row>
                        <b-form-row>
                          <div class="col-md-12" v-if="!viewOnly">
                            <div class="float-right" style="padding-top: 10px;">
                              <b-btn variant="primary" @click.prevent="addSubobjective(index)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Criterio</b-btn>
                            </div>
                          </div>
                        </b-form-row>
                        <b-form-row style="padding-top: 15px;">
                          <b-form-feedback class="d-block" v-if="form.errorsFor(`stages.${index}.criterion`)" style="padding-bottom: 10px;">
                            {{ form.errorsFor(`stages.${index}.criterion`) }}
                          </b-form-feedback>
                          <template v-for="(criterion, index2) in stage.criterion" style="padding-right: 15px;">
                            <b-card no-body class="mb-2 border-secondary" :key="criterion.key" style="width: 100%;">
                              <b-card-header class="bg-secondary">
                                <b-row>
                                  <b-col cols="10" class="d-flex justify-content-between"> {{ form.stages[index].criterion[index2].description ? form.stages[index].criterion[index2].description : `Nuevo Criterio ${index2 + 1}` }}</b-col>
                                  <b-col cols="2">
                                    <div class="float-right">
                                      <b-button-group>
                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + criterion.key+'-1'" variant="link">
                                          <span class="collapse-icon"></span>
                                        </b-btn>
                                        <b-btn @click.prevent="removeSubobjective(index, index2)" 
                                          v-if="!viewOnly"
                                          size="sm" 
                                          variant="secondary icon-btn borderless"
                                          v-b-tooltip.top title="Eliminar Criterio">
                                            <span class="ion ion-md-close-circle"></span>
                                        </b-btn>
                                      </b-button-group>
                                    </div>
                                  </b-col>
                                </b-row>
                              </b-card-header>
                              <b-collapse :id="`accordion${criterion.key}-1`" visible :accordion="`accordion-1234`">
                                <b-card-body>
                                  <b-form-row>
                                    <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.stages[index].criterion[index2].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`stages.${index}.criterion.${index2}.description`)" rows="1"></vue-textarea>
                                  </b-form-row>
                                  <b-form-row>
                                    <div class="col-md-12" v-if="!viewOnly">
                                      <div class="float-right" style="padding-top: 10px;">
                                        <b-btn variant="primary" @click.prevent="addItem(index, index2)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Item</b-btn>
                                      </div>
                                    </div>
                                  </b-form-row>
                                  <b-form-row style="padding-top: 15px;">
                                    <b-form-feedback class="d-block" v-if="form.errorsFor(`stages.${index}.criterion.${index2}.items`)" style="padding-bottom: 10px;">
                                      {{ form.errorsFor(`stages.${index}.criterion.${index2}.items`) }}
                                    </b-form-feedback>
                                    <div class="table-responsive" style="padding-right: 15px;">
                                      <table class="table table-bordered table-hover" v-if="criterion.items.length > 0">
                                        <thead class="bg-secondary">
                                          <tr>
                                            <th scope="col" class="align-middle" v-if="!viewOnly">#</th>
                                            <th scope="col" class="align-middle">Descripción</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <template v-for="(item, index3) in criterion.items">
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
                                                <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.stages[index].criterion[index2].items[index3].description" label="" name="description" placeholder="Descripción" :error="form.errorsFor(`stages.${index}.criterion.${index2}.items.${index3}.description`)" rows="1"></vue-textarea>
                                              </td>
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
        </b-card-body>
      </b-collapse>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    VueCheckboxSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    moduleId: { type: Number, default: 0 },
    viewOnly: { type: Boolean, default: false },
    evaluation: {
      default() {
        return {
          name: '',
          module_id: '',
          stages: [
          ]
        };
      }
    }
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
        form: Form.makeFrom(this.evaluation, this.method),
        blockInterval: ''
    };
  },
  created() {
    console.log(this.form)
    if (this.isEdit)
    {
      Alerts.warning('Información', 'Estimado usuario mientras esté editando este formato no podrán realizarse evaluaciones, al culminar debe darle en el botón de finalizar para desbloquearlo.');

      this.blockInterval = setInterval(this.blockEvaluation, 10000);
    }
  },
  beforeDestroy() {
    clearInterval( this.blockInterval )
  },
  methods: {
    blockEvaluation() {
      axios.post('/biologicalmonitoring/audiometry/evaluation/block', { id: this.evaluation.id })
        .then(response => {      
        })
        .catch(error => {
        });
    },
    submit(e) {
      this.loading = true;
      this.form.module_id = this.moduleId;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "audiometry-evaluations" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addObjetive() {
        this.form.stages.push({
            key: new Date().getTime(),
            description: '',
            criterion: []
        })
    },
    removeObjetive(index)
    {
      if (this.form.stages[index].id != undefined)
        this.form.delete.stages.push(this.form.stages[index].id)

      this.form.stages.splice(index, 1)
    },
    addSubobjective(index)
    {
        this.form.stages[index].criterion.push({
            key: new Date().getTime(),
            description: '',
            items: []
        })
    },
    removeSubobjective(indexObj, index)
    {
      if (this.form.stages[indexObj].criterion[index].id != undefined)
        this.form.delete.criterion.push(this.form.stages[indexObj].criterion[index].id)

      this.form.stages[indexObj].criterion.splice(index, 1)
    },
    addItem(indexObj, indexSub) 
    {
      this.form.stages[indexObj].criterion[indexSub].items.push({
          key: new Date().getTime(),
          description: '',
      })
    },
    removeItem(indexObj, indexSub, index)
    {
      if (this.form.stages[indexObj].criterion[indexSub].items[index].id != undefined)
        this.form.delete.items.push(this.form.stages[indexObj].criterion[indexSub].items[index].id)

      this.form.stages[indexObj].criterion[indexSub].items.splice(index, 1)
    }
  }
};
</script>
