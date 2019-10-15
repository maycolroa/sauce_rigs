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
          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.employee_regional_id" :error="form.errorsFor('employee_regional_id')" :selected-object="form.multiselect_regional" name="employee_regional_id" :label="keywordCheck('regional')" :placeholder="`Seleccione la ${this.keywordCheck('regional')}`" :url="regionalsDataUrl" :multiple="true" :allowEmpty="true">
                </vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly || (form.employee_regional_id && form.employee_regional_id.length == 0)" class="col-md-6" v-model="form.employee_headquarter_id" :error="form.errorsFor('employee_headquarter_id')" :selected-object="form.multiselect_sede" name="employee_headquarter_id" label="Sede" placeholder="`Seleccione la sede`" :url="headquartersDataUrl" :parameters="{regional: form.employee_regional_id }" :multiple="true" :allowEmpty="true">
                </vue-ajax-advanced-select>
          </b-form-row>

          <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly || (form.employee_headquarter_id &&form.employee_headquarter_id.length == 0)" class="col-md-6" v-model="form.employee_process_id" :error="form.errorsFor('employee_process_id')" :selected-object="form.multiselect_proceso" name="employee_process_id" label="Proceso" placeholder="Seleccione el proceso" :url="processesDataUrl" :parameters="{headquarter: form.employee_headquarter_id }" :multiple="true" :allowEmpty="true">
            </vue-ajax-advanced-select>
            <vue-ajax-advanced-select :disabled="viewOnly || (form.employee_process_id && form.employee_process_id.length == 0)" class="col-md-6" v-model="form.employee_area_id" :error="form.errorsFor('employee_area_id')" :selected-object="form.multiselect_area" name="employee_area_id" label="Área" placeholder="Seleccione el área" :url="areasDataUrl" :parameters="{process: form.employee_process_id, headquarter: form.employee_headquarter_id }" :multiple="true" :allowEmpty="true">
            </vue-ajax-advanced-select>
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
              <p class="mb-0">Temas a evaluar</p>
            </blockquote>
            <b-form-row>
              <div class="col-md-12" v-if="!viewOnly">
                <div class="float-right" style="padding-top: 10px;">
                  <b-btn variant="primary" @click.prevent="addTheme()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Tema</b-btn>
                </div>
              </div>
            </b-form-row>
            <b-form-row style="padding-top: 15px;">
              <b-form-feedback class="d-block" v-if="form.errorsFor(`themes`)" style="padding-bottom: 10px;">
                {{ form.errorsFor(`themes`) }}
              </b-form-feedback>
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
                              <b-btn @click.prevent="removeTheme(index)" 
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
                    <b-collapse :id="`accordion${theme.key}-1`" visible :accordion="`accordion-123`">
                      <b-card-body>
                        <b-form-row>
                          <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.themes[index].name" label="Nombre de tema" type="text" name="name" :error="form.errorsFor(`themes.${index}.name`)" placeholder="Nombre de tema"></vue-input>
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
                          <div class="table-responsive" style="padding-right: 15px;">
                            <table class="table table-bordered table-hover" v-if="theme.items.length > 0">
                              <thead class="bg-secondary">
                                <tr>
                                  <th scope="col" class="align-middle" v-if="!viewOnly">#</th>
                                  <th scope="col" class="align-middle">Descripción</th>
                                </tr>
                              </thead>
                              <tbody>
                                <template v-for="(item, index2) in theme.items">
                                  <tr :key="index2">
                                    <td class="align-middle" v-if="!viewOnly">
                                      <b-btn @click.prevent="removeItem(index, index2)" 
                                        size="xs" 
                                        variant="outline-primary icon-btn borderless"
                                        v-b-tooltip.top title="Eliminar Item">
                                        <span class="ion ion-md-close-circle"></span>
                                      </b-btn>
                                    </td>
                                    <td style="padding: 0px;">
                                      <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.themes[index].items[index2].description" label="" name="description" placeholder="Descripción" :error="form.errorsFor(`themes.${index}.items.${index2}.description`)" rows="1"></vue-textarea>
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
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueInput,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    regionalsDataUrl: { type: String, default: "" },
    headquartersDataUrl: { type: String, default: "" },
    areasDataUrl: { type: String, default: "" },
    processesDataUrl: { type: String, default: "" },
    inspection: {
      default() {
        return {
          name: '',
          employee_regional_id: [],
          employee_headquarter_id: [],
          employee_area_id: [],
          employee_process_id: [],
          themes: []
        };
      }
    }
  },
  watch: {
    inspection() {
      this.loading = false;
      this.form = Form.makeFrom(this.inspection, this.method);
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.inspection, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "dangerousconditions-inspections" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addTheme() {
        this.form.themes.push({
            key: new Date().getTime(),
            name: '',
            items: []
        })
    },
    removeTheme(index)
    {
      if (this.form.themes[index].id != undefined)
        this.form.delete.themes.push(this.form.themes[index].id)

      this.form.themes.splice(index, 1)
    },
    addItem(index)
    {
        this.form.themes[index].items.push({
            key: new Date().getTime(),
            description: ''
        })
    },
    removeItem(indexTheme, index)
    {
      if (this.form.themes[indexTheme].items[index].id != undefined)
        this.form.delete.items.push(this.form.themes[indexTheme].items[index].id)

      this.form.themes[indexTheme].items.splice(index, 1)
    }
  }
};
</script>
