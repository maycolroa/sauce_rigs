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

      <b-form-row>
        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>

        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.participants"  name="participants" :error="form.errorsFor(`participants`)" label="Participantes" placeholder="Seleccione los participantes" url="/selects/tagsParticipants" :multiple="true" :allowEmpty="true" :taggable="true">
                    </vue-ajax-advanced-select>

      </b-form-row>

      <b-form-row>
        <div class="col-md-12" v-if="!viewOnly">
          <div class="float-right" style="padding-top: 20px;">
            <b-btn variant="primary" @click.prevent="addActiviy()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Actividad</b-btn>
          </div>
        </div>
      </b-form-row>

      <b-form-row>
        <b-card no-body variant="white" class="mb-3" style="width: 100%;">
          <b-tabs card pills class="nav-responsive-md md-pills-light">
            <b-tab 
                v-for="(activity, index) in form.activities"
                :key="activity.key">
                <template slot="title">
                  <strong>{{ form.activities[index].activity.name ? form.activities[index].activity.name : `Nuevo Actividad ${index + 1}` }}</strong> 
                  <b-btn @click.prevent="removeActivity(index)" 
                    v-if="form.activities.length > 1 && !viewOnly"
                    size="sm" 
                    variant="outline-primary icon-btn borderless"
                    v-b-tooltip.top title="Eliminar Actividad">
                    <span class="ion ion-md-close-circle"></span>
                  </b-btn>
                </template>
                  
                <form-activity-component
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
        <vue-textarea class="col-md-12" v-model="form.changeHistory" label="Detalle de cambios realizados" name="changeHistory" :error="form.errorsFor('changeHistory')" placeholder="Detalle de cambios realizados"></vue-textarea>
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
          <b-btn v-show="false" @click="submit(false)" :disabled="loading" variant="primary" v-if="!viewOnly">Guardar y continuar</b-btn>&nbsp;&nbsp;

          <b-btn @click="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
        </template>
      </div>
    </b-form>
  </div>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";
import FormActivityComponent from '@/components/IndustrialSecure/DangerMatrix/FormActivityComponent.vue';
import ModalsCreateComponent from '@/components/IndustrialSecure/DangerMatrix/ModalsCreateComponent.vue';
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
  components: {
    VueInput,
    FormActivityComponent,
    ModalsCreateComponent,
    LocationLevelComponent,
    VueTextarea,
    VueAjaxAdvancedSelect
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
    dangerMatrix: {
      default() {
        return {
            locations: {
              employee_regional_id: '',
              employee_headquarter_id: '',
              employee_area_id: '',
              employee_process_id: ''
            },
            name: '',
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
            changeHistory: ''
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
      configLocation: {}
    };
  },
  methods: {
    submit(redirect = true) {
      this.loading = true;
      this.form
        .submit(this.url)
        .then(response => {
          this.loading = false;

          if (redirect)
            this.$router.push({ name: "industrialsecure-dangermatrix" });
        })
        .catch(error => {
          this.loading = false;
        });
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
        }
      })
    },
    removeActivity(index) {
      if (this.dangerMatrix.activities[index].id != '')
        this.form.activitiesRemoved.push(this.dangerMatrix.activities[index])
      this.dangerMatrix.activities.splice(index, 1)
    },
    updateActivityNameTab(values, index) {
      this.form.activities[index].activity.name = values
    },
    setConfigLocation(value)
    {
      this.configLocation = value
    }
  }
};
</script>
