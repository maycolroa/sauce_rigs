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
            <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
          </b-form-row>
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
              <p class="mb-0">Temas del informe</p>
            </blockquote>
            <b-form-row>
              <div class="col-md-12" v-if="!viewOnly">
                <div class="float-right" style="padding-top: 10px;">
                  <b-btn variant="primary" @click.prevent="addObjetive()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Tema</b-btn>
                </div>
              </div>
            </b-form-row>
            <b-form-row style="padding-top: 15px;">
              <b-form-feedback class="d-block" v-if="form.errorsFor(`themes`)" style="padding-bottom: 10px;">
                {{ form.errorsFor(`themes`) }}
              </b-form-feedback>
              <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                <template v-for="(objetive, index) in form.themes">
                  <b-card no-body class="mb-2 border-secondary" :key="objetive.key" style="width: 100%;">
                    <b-card-header class="bg-secondary">
                      <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> {{ form.themes[index].description ? form.themes[index].description : `Nuevo tema ${index + 1}` }}</b-col>
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
                                v-b-tooltip.top title="Eliminar Tema">
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
                          <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.themes[index].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`themes.${index}.description`)" rows="1"></vue-textarea>
                        </b-form-row>
                        <b-form-row>
                          <div class="col-md-12" v-if="!viewOnly">
                            <div class="float-right" style="padding-top: 10px;">
                              <b-btn variant="primary" @click.prevent="addItem(index)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Item</b-btn>
                            </div>
                          </div>
                        </b-form-row>
                        <b-form-row style="padding-top: 15px;">
                          <b-form-feedback class="d-block" v-if="form.errorsFor(`themes.${index}.items`)" style="padding-bottom: 10px;">
                            {{ form.errorsFor(`themes.${index}.items`) }}
                          </b-form-feedback>
                          <template v-for="(item, index2) in objetive.items" style="padding-right: 15px;">
                            <b-card no-body class="mb-2 border-secondary" :key="item.key" style="width: 100%;">
                              <b-card-header class="bg-secondary">
                                <b-row>
                                  <b-col cols="10" class="d-flex justify-content-between"> {{ form.themes[index].items[index2].description ? form.themes[index].items[index2].description : `Nuevo Item ${index2 + 1}` }}</b-col>
                                  <b-col cols="2">
                                    <div class="float-right">
                                      <b-button-group>
                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + item.key+'-1'" variant="link">
                                          <span class="collapse-icon"></span>
                                        </b-btn>
                                        <b-btn @click.prevent="removeItem(index, index2)" 
                                          v-if="!viewOnly"
                                          size="sm" 
                                          variant="secondary icon-btn borderless"
                                          v-b-tooltip.top title="Eliminar Item">
                                            <span class="ion ion-md-close-circle"></span>
                                        </b-btn>
                                      </b-button-group>
                                    </div>
                                  </b-col>
                                </b-row>
                              </b-card-header>
                              <b-collapse :id="`accordion${item.key}-1`" visible :accordion="`accordion-1234`">
                                <b-card-body>
                                  <b-form-row>
                                    <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.themes[index].items[index2].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`themes.${index}.items.${index2}.description`)" rows="1"></vue-textarea>
                                  </b-form-row>
                                  <b-form-row>
                                    <vue-radio :disabled="viewOnly" class="col-md-12" v-model="form.themes[index].items[index2].show_program_value" :options="siNo" :name="`show_program_value${index}${index2}`" :error="form.errorsFor(`themes.${index}.items.${index2}.show_program_value`)" label="Mostrar Valor Programado" :checked="form.themes[index].items[index2].show_program_value">
                                    </vue-radio>
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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueInput,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    inform: {
      default() {
        return {
          name: '',
          themes: []
        };
      }
    }
  },
  watch: {
    inform() {
      this.loading = false;
      this.form = Form.makeFrom(this.inform, this.method);
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.inform, this.method),
        siNo: [
          {text: 'SI', value: 'SI'},
          {text: 'NO', value: 'NO'}
        ]
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-informs" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addObjetive() {
        this.form.themes.push({
            key: new Date().getTime(),
            description: '',
            items: []
        })
    },
    removeObjetive(index)
    {
      if (this.form.themes[index].id != undefined)
        this.form.delete.themes.push(this.form.themes[index].id)

      this.form.themes.splice(index, 1)
    },
    addItem(indexObj) 
    {
      this.form.themes[indexObj].items.push({
          key: new Date().getTime(),
          description: '',
      })
    },
    removeItem(indexObj, index)
    {
      if (this.form.themes[indexObj].items[index].id != undefined)
        this.form.delete.items.push(this.form.themes[indexObj].items[index].id)

      this.form.themes[indexObj].items.splice(index, 1)
    }
  }
};
</script>
