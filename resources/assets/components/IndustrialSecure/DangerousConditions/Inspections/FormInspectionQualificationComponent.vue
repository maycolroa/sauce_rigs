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
          :qualification="qualification"/>
          <b-form-row>
            <vue-radio :checked="form.state" class="col-md-12" v-model="form.state" :options="states" name="state" :error="form.errorsFor('location_level_form')" label="Estado" @input="saveState()">
              </vue-radio>
          </b-form-row> 
          <b-form-row v-if="form.state == 'Rechazada'">
              <vue-textarea class="col-md-12" v-model="form.motive" label="Motivo de rechazo" name="v" type="text" placeholder="Motivo" :error="form.errorsFor('motive')" @onBlur="saveState()"></vue-textarea>
          </b-form-row>
        </b-card-body>
      </b-collapse>
    </b-card>

    <b-card v-if="form.add_fields && form.add_fields.length > 0" no-body class="mb-2 border-secondary" style="width: 100%;">
     <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between"> Campos adicionales </b-col>
          <b-col cols="1">
            <div class="float-right">
              <b-button-group>
                <b-btn href="javascript:void(0)" v-b-toggle="'accordion-field'" variant="link">
                  <span class="collapse-icon"></span>
                </b-btn>
              </b-button-group>
            </div>
          </b-col>
        </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-field`" visible :accordion="`accordion-master`">
        <b-card-body>
          <template v-for="(field, index) in form.add_fields">
            <div :key="index.key">
                <b-form-row>
                    <vue-textarea :disabled="true" class="col-md-12" v-model="field.value" :label="field.name" name="name" type="text" placeholder="Nombre" :error="form.errorsFor(`field.${index}.name`)"></vue-textarea>
                </b-form-row>
            </div>
          </template>
        </b-card-body>
      </b-collapse>
    </b-card>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
          <b-row>
            <b-col cols="11" class="d-flex justify-content-between"> Temas </b-col>
            <b-col cols="1">
                <div class="float-right">
                  <b-button-group>
                    <b-btn href="javascript:void(0)" v-b-toggle="'accordion-themes'" variant="link">
                      <span class="collapse-icon"></span>
                    </b-btn>
                  </b-button-group>
                </div>
            </b-col>
          </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-themes`" visible :accordion="`accordion-master`">
        <b-card-body>
          <div class="col-md-12">
            <blockquote class="blockquote text-center">
              <p class="mb-0">Temas a evaluar</p>
            </blockquote>
            <b-form-row style="padding-top: 15px;">
              <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                <template v-for="(theme, index) in form.themes">
                  <b-card no-body class="mb-2 border-secondary" :key="theme.key" style="width: 100%;">
                    <b-card-header class="bg-secondary">
                      <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> {{ form.themes[index].name ? form.themes[index].name : `Nombre del tema ${index + 1}` }}</b-col>
                        <b-col cols="2">
                          <div class="float-right">
                            <b-button-group>
                              <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + theme.key+'-1'" variant="link">
                                <span class="collapse-icon"></span>
                              </b-btn>
                            </b-button-group>
                          </div>
                        </b-col>
                      </b-row>
                    </b-card-header>
                    <b-collapse :id="`accordion${theme.key}-1`" visible :accordion="`accordion-123`">
                      <b-card-body>
                        <b-form-row>
                          <vue-input :disabled="true" class="col-md-12" v-model="form.themes[index].name" label="Nombre de tema" type="text" name="name" placeholder="Nombre de tema"></vue-input>
                        </b-form-row>
                        <b-form-row style="padding-top: 15px;">
                          <div class="table-responsive" style="padding-right: 15px;">
                            <table class="table table-bordered table-hover" v-if="theme.items.length > 0">
                              <thead class="bg-secondary">
                                <tr>
                                  <th scope="col" class="align-middle" v-if="!viewOnly">#</th>
                                  <th scope="col" class="align-middle">Descripción</th>
                                  <th scope="col" class="align-middle">Calificación</th>
                                  <th scope="col" class="align-middle">Nivel de Riesgo</th>
                                  <th scope="col" class="align-middle">Observación</th>
                                </tr>
                              </thead>
                              <tbody>
                                <template v-for="(item, index2) in theme.items">
                                  <tr :key="index2">
                                    <td class="align-middle" v-if="!viewOnly">
                                      <!-- Planes de acción -->
                                      <b-btn v-if="!viewOnly" @click="showModal(`modalPlan${index}-${index2}`)" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Ver plan de acción"><span class="ion ion-md-eye"></span></b-btn>

                                      <b-modal :ref="`modalPlan${index}-${index2}`" :hideFooter="true" :id="`modals-default-${index+1}${index2}`" class="modal-top" size="lg" @hidden="removed(index, index2)">
                                        <div slot="modal-title">
                                          Plan de acción<br>
                                          <small class="text-muted">Crea planes de acción para tu justificación.</small>
                                        </div>

                                        <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                          <action-plan-component
                                            :is-edit="!viewOnly"
                                            :view-only="viewOnly"
                                            :form="form"
                                            :prefix-index="`themes.${index}.items.${index2}.`"
                                            :action-plan-states="actionPlanStates"
                                            v-model="item.actionPlan"
                                            :action-plan="item.actionPlan"/>
                                        </b-card>
                                        <br>
                                        <div class="row float-right pt-12 pr-12y">
                                          <b-btn variant="primary" @click="hideModal(`modalPlan${index}-${index2}`)">Cerrar</b-btn>
                                        </div>
                                      </b-modal>

                                       <!-- Imagenes -->
                                      <b-btn v-if="!viewOnly" @click="showModal(`modalImages${index}-${index2}`)" variant="outline-success icon-btn borderless" size="xs" v-b-tooltip.top title="Ver Imagenes"><span class="ion ion-md-image"></span></b-btn>

                                      <b-modal :ref="`modalImages${index}-${index2}`" :hideFooter="true" :id="`modals-default-${index+1}${index2}`" class="modal-top" size="lg">
                                        <div slot="modal-title">
                                          Imagenes
                                        </div>

                                        <b-card bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                                          <b-row style="padding-bottom: 35px;">
                                            <b-col>
                                              <blockquote class="blockquote text-center">
                                                <p class="mb-0">Imagen 1</p>
                                              </blockquote>
                                              <form-image
                                                :url="`/industrialSecurity/dangerousConditions/inspection/qualification/saveImage`"
                                                :urlDownload="`/industrialSecurity/dangerousConditions/inspection/qualification/downloadImage/${item.id_item_qualification}/photo_1`"
                                                column="photo_1"
                                                :id="item.id_item_qualification"
                                                :image="item.photo_1"
                                                :path="item.path_1"
                                                :old="item.old_1"/>
                                            </b-col>
                                          </b-row>
                                          <b-row>
                                            <b-col>
                                              <blockquote class="blockquote text-center">
                                                <p class="mb-0">Imagen 2</p>
                                              </blockquote>
                                              <form-image
                                                :url="`/industrialSecurity/dangerousConditions/inspection/qualification/saveImage`"
                                                :urlDownload="`/industrialSecurity/dangerousConditions/inspection/qualification/downloadImage/${item.id_item_qualification}/photo_2`"
                                                column="photo_2"
                                                :id="item.id_item_qualification"
                                                :image="item.photo_2"
                                                :path="item.path_2"
                                                :old="item.old_2"/>
                                            </b-col>
                                          </b-row>
                                        </b-card>
                                        <br>
                                        <div class="row float-right pt-12 pr-12y">
                                          <b-btn variant="primary" @click="hideModal(`modalImages${index}-${index2}`)">Cerrar</b-btn>
                                        </div>
                                      </b-modal>
                                    </td>
                                    <td style="padding: 0px;">
                                      <vue-textarea :disabled="true" class="col-md-12" v-model="form.themes[index].items[index2].description" label="" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                                      <div v-if="existError(`themes.${index}.items.${index2}.`)">
                                          <b-form-feedback class="d-block" style="padding-bottom: 10px;">
                                            Este item contiene errores en sus datos
                                          </b-form-feedback>
                                      </div>
                                    </td>
                                    <td style="padding: 0px;">
                                      <vue-input :disabled="true" class="col-md-12" v-model="form.themes[index].items[index2].qualification" label="" type="text" name="qualification" placeholder="Calificación"></vue-input>
                                    </td>
                                    <td style="padding: 0px;">
                                      <vue-input :disabled="true" class="col-md-12" v-model="form.themes[index].items[index2].level_risk" label="" type="text" name="level_risk" placeholder="Nivel de Riesgo"></vue-input>
                                    </td>
                                    <td style="padding: 0px;">
                                      <vue-textarea :disabled="true" class="col-md-12" v-model="form.themes[index].items[index2].find" label="" name="find" placeholder="Observación" rows="1"></vue-textarea>
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
              </perfect-scrollbar>
            </b-form-row>
          </div>
        </b-card-body>
      </b-collapse>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import Alerts from '@/utils/Alerts.js';
import InformationGeneral from "./InformationGeneral.vue";
import FormImage from '../FormImageComponent.vue';
import VueRadio from "@/components/Inputs/VueRadio.vue";

export default {
  components: {
    VueInput,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    ActionPlanComponent,
    InformationGeneral,
    FormImage,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    actionPlanStates: {
      type: Array,
      default: function() {
        return [];
      }
    },
    qualification: {
      default() {
        return {
          state: '',
          motive: '',
          themes: [],
          add_fields: []
        };
      }
    }
  },
  watch: {
    qualification() {
      this.loading = false;
      this.form = Form.makeFrom(this.qualification, this.method, false, false);

      setTimeout(() => {
          this.ready = true
      }, 5000)
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.qualification, this.method, false, false),
        states: [
          {text: 'Aprobada', value: 'Aprobada'},
          {text: 'Rechazada', value: 'Rechazada'}
        ],
    };
  },
  methods: {
    showModal(ref) {
			this.$refs[ref][0].show()
		},
		hideModal(ref) {
			this.$refs[ref][0].hide()
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
    removed(index, index2) {
      let keys = [];
      
      this.form.themes[index].items[index2].actionPlan.activities.forEach((activity, keyAct) => {
        if (activity.description && activity.responsible_id && activity.expiration_date && activity.state)
        {
          keys.push(activity);
        }
      });

      this.form.themes[index].items[index2].actionPlan.activities.splice(0);

      keys.forEach((item, key) => {
        this.form.themes[index].items[index2].actionPlan.activities.push(item)
      });

      this.saveActionPlan(index, index2);
    },
    saveActionPlan(indexTheme, indexItem)
    {
      if (this.ready)
      {
        this.loading = true;
        let item = this.form.themes[indexTheme].items[indexItem];
        
        let data = new FormData();
        data.append('id_item_qualification', item.id_item_qualification);
        data.append('actionPlan', JSON.stringify(item.actionPlan));
        data.append(`themes[${indexTheme}][${indexItem}]`, JSON.stringify({ actionPlan: item.actionPlan }));

        this.form.resetError()
        this.form
          .submit('/industrialSecurity/dangerousConditions/inspection/qualification/saveQualification', false, data)
          .then(response => {
            _.forIn(response.data.data, (value, key) => {
              item[key] = value
            })

            this.loading = false;
            
          })
          .catch(error => {
            this.loading = false;
          });
      }
    },
    saveState()
    {
        let data = new FormData();
        data.append('state', this.form.state);
        data.append('motive', this.form.motive);
        data.append('qualification_date', this.form.qualification_date);

        this.form.resetError()
        this.form
          .submit('/industrialSecurity/dangerousConditions/inspection/qualification/saveQualificationState', false, data)
          .then(response => {
            this.form.state = response.data.data.state;
            this.form.motive = response.data.data.motive;

            this.loading = false;
            
          })
          .catch(error => {
            this.loading = false;
          });
    }
  }
};
</script>
