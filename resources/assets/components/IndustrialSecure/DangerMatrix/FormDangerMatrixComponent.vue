<template>
  <div>
    <b-row v-if="!viewOnly">
      <b-col>
        <div class="float-right" style="padding-bottom: 20px;">
            <modals-create-component
              :conf-location="configLocation"/>
        </div>
      </b-col>
    </b-row>

    <b-form :action="url" @submit.prevent="submit" autocomplete="off">

      <b-form-row>
        <vue-radio v-if="auth.can['dangerMatrix_approved_matrix']" :disabled="viewOnly" :checked="form.approved" class="col-md-6" v-model="form.approved" :options="siNo" name="approved" :error="form.errorsFor('approved')" label="¿Aprobar mátriz?">
          </vue-radio>
        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.year" label="Año" type="text" name="year" :error="form.errorsFor('year')" placeholder="Año"></vue-input>
      </b-form-row>
      
      <b-form-row>
        <location-level-component
          :is-edit="isEdit"
          :view-only="viewOnly"
          v-model="form.locations"
          :location-level="dangerMatrix.locations"
          :form="form"
          application="industrialSecure"
          module="dangerMatrix"
          @configLocation="setConfigLocation"/>
      </b-form-row>

      <b-form-row v-show="this.fields.length > 0">
        <template v-for="(field, index) in this.fields">
          <div class="col-md-6" :key="index.key">
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="field.value" :label="field.name" name="fieldname" type="text" placeholder="Seleccione" :error="form.errorsFor(`field.${index}.fieldname`)" :url="`/selects/tagsAddFields/${field.id}`" :allowEmpty="true" :taggable="true" :multiple="true"></vue-ajax-advanced-select>
          </div>
        </template>
      </b-form-row>

      <b-form-row>
        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>

        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.participants"  name="participants" :error="form.errorsFor(`participants`)" label="Participantes" placeholder="Seleccione los participantes" url="/selects/tagsParticipants" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>

      </b-form-row>

      <b-card v-if="isEdit || viewOnly" bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
        <b-form-row>
          <vue-input :disabled="false" class="col-md-12" v-model="search_keyword" label="Buscar" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre" help-text="Se buscara en los campos Fuente generadora y Controles existentes"></vue-input>
          <div class="float-center" style="padding-top: 20px;">
            <b-btn variant="primary" @click="searchkeywordShow('modalHistorial')">&nbsp;&nbsp;Buscar</b-btn>
          </div>
        </b-form-row>

        <b-modal ref="modalHistorial" :hideFooter="true" id="modals-historial" class="modal-top" size="lg">
						<div slot="modal-title">
							Se encontro la palabra <b>{{search_keyword}}</b> en:
						</div>

						<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
              <table class="table table-bordered table-hover">
                <thead class="bg-secondary">
                  <tr>
                    <th>Actividad</th>
                    <th>Peligro</th>
                    <th>Campo</th>
                    <th>Valor</th>
                    <th>Buscar</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="row in sharedData">
                    <tr :key="row.key">
                      <td>{{row.activity}}</td>
                      <td>{{row.danger}}</td>
                      <td>{{row.campo}}</td>
                      <td>{{row.value}}</td>
                      <td>
                        <b-btn @click="showRow(row.id_activity, row.id_danger, 'modalHistorial', row.campo)" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Buscar"><span class="ion ion-ios-copy"></span></b-btn>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
						</b-card>
						<br>
						<div class="row float-right pt-12 pr-12y">
							<b-btn variant="primary" @click="searchkeywordHide('modalHistorial')">Cerrar</b-btn>
						</div>
				</b-modal>
      </b-card>

      <b-form-row>
        <div class="col-md-12" v-if="!viewOnly">
          <div class="float-right" style="padding-top: 20px;">
            <b-btn variant="primary" @click.prevent="addActiviy()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Actividad</b-btn>
          </div>
        </div>
      </b-form-row>

      <b-form-row>
        <b-card no-body variant="white" class="mb-3" style="width: 100%;">
          <b-tabs card pills class="nav-responsive-md md-pills-light" ref="tabActivity">
            <b-tab 
                v-for="(activity, index) in form.activities"
                :key="activity.key"
                :active="activity.activate">
                <template slot="title">
                  <strong>{{ form.activities[index].activity.name ? form.activities[index].activity.name : `Nuevo Actividad ${index + 1}`}}</strong> 
                  <b-btn @click.prevent="removeActivity(index)" 
                    v-if="form.activities.length > 1 && !viewOnly"
                    size="sm" 
                    variant="outline-primary icon-btn borderless"
                    v-b-tooltip.top title="Eliminar Actividad">
                    <span class="ion ion-md-close-circle"></span>
                  </b-btn>
                </template>
                  
                <form-activity-component
                  :ref="`activity-${activity.id ? activity.id : activity.key}`"
                  :is-edit="isEdit"
                  :view-only="viewOnly"
                  :activity="activity"
                  v-model="form.activities[index]"
                  :type-activities="typeActivities"
                  :danger-generated="dangerGenerated"
                  :si-no="siNo"
                  :qualifications="qualifications"
                  :action-plan-states="actionPlanStates"
                  :form="form"
                  :index-activity="index"
                  @activityName="updateActivityNameTab"
                  :configuration="configuration"
                />
            </b-tab>
          </b-tabs>
        </b-card>
      </b-form-row>

      <b-form-row v-if="isEdit">
        <vue-ajax-advanced-select class="col-md-12" v-model="form.changeHistory" name="danger_description" :error="form.errorsFor('changeHistory')" label="Detalle de cambios realizados" placeholder="Seleccione los detalles de cambios realizados" :url="tagsHistoryChangeDataUrl" :multiple="true" :allowEmpty="true" :taggable="true"></vue-ajax-advanced-select>
      </b-form-row>

      <b-form-row v-if="viewOnly">
        <div class="col-md-12">
          <h4 class="font-weight-bold mb-1">
            Historial de cambios realizados
          </h4>
          <div class="col-md">
            <b-card no-body>
              <b-card-body>
                  <vue-table
                      configName="industrialsecure-dangermatrix-history"
                      :modelId="form.id ? form.id : -1"
                      ></vue-table>
              </b-card-body>
          </b-card>
          </div>
        </div>
      </b-form-row>

      <div class="row float-right pt-10 pr-10">
        <template>
          <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
          <b-btn @click="submit(false)" :disabled="loading" variant="primary" v-if="!viewOnly">Guardar y continuar</b-btn>&nbsp;&nbsp;
          <b-btn @click="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
        </template>
      </div>
    </b-form>
  </div>
</template>

<script>
import Vue from 'vue'
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import FormActivityComponent from '@/components/IndustrialSecure/DangerMatrix/FormActivityComponent.vue';
import ModalsCreateComponent from '@/components/IndustrialSecure/DangerMatrix/ModalsCreateComponent.vue';
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Alerts from '@/utils/Alerts.js';

export default {
  components: {
    VueInput,
    VueRadio,
    FormActivityComponent,
    ModalsCreateComponent,
    LocationLevelComponent,
    VueAjaxAdvancedSelect,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    userDataUrl: { type: String, default: "" },
    typeActivities: {
      type: Array,
      default: function() {
        return [];
      }
    },
    dangerGenerated: {
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
    qualifications: {
      type: [Array, Object],
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
    configuration: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    fields: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    dangerMatrix: {
      default() {
        return {
            locations: {
              employee_regional_id: '',
              employee_headquarter_id: '',
              employee_area_id: '',
              employee_process_id: ''
            },
            add_fields: '',
            year: '',
            name: '',
            approved: '',
            participants: '',
            activities: [
              {
                key: new Date().getTime(),
                id: '',
                activity_id: '',
                type_activity: '',
                dangers: [],
                dangersRemoved: [],
                activity: {
                  name: ''
                }
              }
            ],
            activitiesRemoved: [],
            changeHistory: '',
            historial: true
        };
      }
    }
  },
  watch: {
    dangerMatrix() {
      this.form = Form.makeFrom(this.dangerMatrix, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.dangerMatrix, this.method),
      configLocation: {},      
      tagsHistoryChangeDataUrl: '/selects/tagsHistoryChange',
      search_keyword: '',
      part: [],
      sharedData: [],
      postData: {}
    }
  },
  methods: {
    submit(redirect = true) {
      this.loading = true;
      this.form.add_fields = this.fields;
      this.form.historial = redirect;
      let now_year = new Date().getFullYear()

      if (this.isEdit && (this.form.year > 0 && this.form.year < now_year) && this.auth.dangerMatrixBlock == 'SI')
      {
        this.loading = false;
        Alerts.error('Error', 'La configuración del modulo no permite la edicion de matrices pertenecientes a años anteriores al año actual');
      }
      else
      {
        this.form
          .submit(this.url)
          .then(response => {
            this.loading = false;

            if (redirect)
              this.$router.push({ name: "industrialsecure-dangermatrix" });
            else
            {
              _.forIn(response.data.data.original.data, (value, key) => {
                  this.form[key] = value
              })

              _.forIn(response.data.data.original.data, (value, key) => {
                if (key == 'participants')
                {
                  let arrayP = value.split(',');

                  _.forIn(arrayP, (value2) => {
                    this.part.push({
                      name: value2, value: value2
                    })
                  })
                }
                  this.form.participants = this.part
              })

              this.clearAllErrors();
            }
          })
          .catch(error => {
            this.loading = false;
          });
      }
    },
    clearAllErrors() {
      _.forIn(this.form.errors.errors, (value, key) => {
            this.form.errors.errors[key].splice(0, this.form.errors.errors[key].length);
      })
    },
    addActiviy() {
      this.dangerMatrix.activities.push({
        key: new Date().getTime(),
        id: '',
        activity_id: '',
        type_activity: '',
        dangers: [],
        dangersRemoved: [],
        activity: {
          name: ''
        },
        activate:true
      })
    },
    removeActivity(index) {
      
      if (this.form.activities[index].id != '')
        this.form.activitiesRemoved.push(this.form.activities[index])
      this.form.activities.splice(index, 1)
    },
    updateActivityNameTab(values, index) {
      this.form.activities[index].activity.name = values
    },
    setConfigLocation(value)
    {
      this.configLocation = value
    },
    searchkeywordShow(ref)
    {
      //this.$refs.tableDangerMatrix.refresh()

      let postData = Object.assign({}, {keyword: this.search_keyword}, {danger_matrix: this.form.id});

      axios.post('/industrialSecurity/dangersMatrix/searchKeyword/data', postData)
      .then(response => {
          this.sharedData = response.data.data;
          this.$refs[ref].show();
      })
      .catch(error => {
          console.log(error);
          Alerts.error('Error', 'Hubo un problema recolectando la información');
      });
    },
    searchkeywordHide(ref)
    {
      this.$refs[ref].hide();
    },
    showRow(activity, danger, ref, campo)
    {
      _.forIn(this.form.activities, (act) => {
        act.activate = false
      })

      let activar = this.form.activities.findIndex(act => act.id == activity);

      if (activar)
      {
        this.form.activities[activar].activate = true

        _.forIn(this.form.activities[activar].dangers, (d) => {
          d.activate = false
        })

        let activarD = this.form.activities[activar].dangers.findIndex(dang => dang.id == danger);

        this.form.activities[activar].dangers[activarD].activate = true

        this.$refs.tabActivity.setTab(activar);

        if (campo !== 'Fuente Generadora')
          this.$refs[`activity-${activity}`][0].$refs[`danger-${danger}`][0].$refs.wizardFormDanger.activateTab(2)
      }

      this.$refs[ref].hide();
    }
  }
};
</script>
