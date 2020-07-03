<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.rate" :multiple="false" :options="rates" :hide-selected="false" name="rate" :error="form.errorsFor('rate')" label="Severidad" placeholder="Seleccione el grado de severidad">
        </vue-advanced-select>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.condition_id" :error="form.errorsFor('condition_id')" :selected-object="form.multiselect_condition" name="condition_id" label="Condición" placeholder="Seleccione la condición" :url="conditionsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <b-form-row>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.observation" label="Observación" name="observation" :error="form.errorsFor('observation')"  placeholder="Observación"></vue-textarea>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.other_condition" label="Otra Condición" name="other_condition" :error="form.errorsFor(`other_condition`)"  placeholder="Otra Condición"></vue-textarea>
    </b-form-row>
  
    <b-form-row>
      <location-level-component
        :is-edit="isEdit"
        :view-only="viewOnly"
        v-model="form.locations"
        :location-level="report.locations"
        :form="form"/>
    </b-form-row>

    <div class="col-md-12">
      <b-card bg-variant="transparent" border-variant="dark" title="Imágenes" class="mb-3 box-shadow-none">
        <center><h4>Imagen 1</h4></center>
        <div>
          <center>
              <loading :display="loading"/>
              <div class="my-4 mx-2 text-center" v-if="!loading && form.old_1">
                  <img class="mw-100" :src="form.path_1" alt="Max-width 100%">
              </div>
          </center>
          
          <b-form-row>
              <vue-file-simple :help-text="form.old_1 ? `Para descargar la imagen actual, haga click <a href='/industrialSecurity/dangerousConditions/report/downloadImage/${report.id}/image_1' target='blank'>aqui</a> `: null" class="col-md-12" v-model="form.image_1" label="Imagen (*.png, *.jpg, *.jpeg)" name="image_1" :error="form.errorsFor('image_1')" placeholder="Seleccione una imagen" :maxFileSize="10"></vue-file-simple>
          </b-form-row>
        </div>
        <div>
          <center><h4>Imagen 2</h4></center>
          <center>
              <loading :display="loading"/>
              <div class="my-4 mx-2 text-center" v-if="!loading && form.old_2">
                  <img class="mw-100" :src="form.path_2" alt="Max-width 100%">
              </div>
          </center>
          
          <b-form-row>
              <vue-file-simple :help-text="form.old_2 ? `Para descargar la imagen actual, haga click <a href='/industrialSecurity/dangerousConditions/report/downloadImage/${report.id}/image_2' target='blank'>aqui</a> `: null" class="col-md-12" v-model="form.image_2" label="Imagen (*.png, *.jpg, *.jpeg)" name="image_2" :error="form.errorsFor('image_2')" placeholder="Seleccione una imagen" :maxFileSize="10"></vue-file-simple>
          </b-form-row>
        </div>
        <div>
          <center><h4>Imagen 3</h4></center>
          <center>
              <loading :display="loading"/>
              <div class="my-4 mx-2 text-center" v-if="!loading && form.old_3">
                  <img class="mw-100" :src="form.path_3" alt="Max-width 100%">
              </div>
          </center>
          
          <b-form-row>
              <vue-file-simple :help-text="form.old_3 ? `Para descargar la imagen actual, haga click <a href='/industrialSecurity/dangerousConditions/report/downloadImage/${report.id}/image_3' target='blank'>aqui</a> `: null" class="col-md-12" v-model="form.image_3" label="Imagen (*.png, *.jpg, *.jpeg)" name="image_3" :error="form.errorsFor('image_3')" placeholder="Seleccione una imagen" :maxFileSize="10"></vue-file-simple>
          </b-form-row>
        </div>
      </b-card>
    </div>

    <center>
      <div style="padding-top: 20px; padding-bottom: 20px;"><b-btn @click="showModal" variant="primary"><span class="ion ion-md-eye"></span> Crear plan de acción</b-btn></div>
    </center>

    <b-modal ref="modalPlan" :hideFooter="true" id="modals-default" class="modal-top" size="lg">
      <div slot="modal-title">
        Plan de acción<br>
        <small class="text-muted">Crea planes de acción para tu justificación.</small>
      </div>

      <b-card bg-variant="transparent" title="" class="mb-3 box-shadow-none">
        <action-plan-component
          :is-edit="!viewOnly"
          :view-only="viewOnly"
          :form="form"
          :action-plan-states="actionPlanStates"
          v-model="form.actionPlan"
          :action-plan="form.actionPlan"/>
      </b-card>
      <br>
      <div class="row float-right pt-12 pr-12y">
        <b-btn variant="primary" @click="hideModal">Cerrar</b-btn>
      </div>
    </b-modal>


    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';
import Form from "@/utils/Form.js";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueTextarea,
    LocationLevelComponent,
    PerfectScrollbar,
    ActionPlanComponent,
    Loading,
    VueFileSimple
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
    conditionsDataUrl: { type: String, default: "" },
    rates: { 
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
    report: {
      default() {
        return {
          observation: '',
          rate: '',
          condition_id: '',
          other_condition: '',
          locations: {
            employee_regional_id: '',
            employee_headquarter_id: '',
            employee_area_id: '',
            employee_process_id: ''
          },
          actionPlan: {
            activities: [],
            activitiesRemoved: []
          },
          image_1: '',
          path_1: '',
          old_1: '',
          image_2: '',
          path_2: '',
          old_2: '',
          image_3: '',
          path_3: '',
          old_3: '',
        };
      }
    }
  },
  watch: {
    report() {
      this.form = Form.makeFrom(this.report, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.report, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "dangerousconditions-reports" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    showModal() {
      this.$refs.modalPlan.show()
    },
    hideModal() {
      this.$refs.modalPlan.hide()
    }
  }
};
</script>
