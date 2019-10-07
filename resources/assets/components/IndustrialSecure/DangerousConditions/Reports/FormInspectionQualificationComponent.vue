<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <div class="col-md-12">
      <b-card bg-variant="transparent" border-variant="dark" title="Información General" class="mb-3 box-shadow-none">
        <b-row>
          <b-col class="text-left">
            <div><b>Severidad:</b> <br>{{ report.rate }}</div>
            <div><b>Observación:</b> <br>{{ report.observation }}</div>
            <div><b>Condición:</b> <br>{{ report.condition ? report.condition.description : '-' }}</div>
            <div><b>Otra Condición:</b> <br>{{ report.other_condition }}</div>
            <div><b>Usuario que reporta:</b> <br>{{ report.user ? report.user.name : '-' }}</div>
          </b-col>
          <b-col class="text-left">
            <div><b>Regional:</b> <br>{{ report.regional ? report.regional.name : '-' }}</div>
            <div><b>Sede:</b> <br>{{ report.headquarter ? report.headquarter.name : '-' }}</div>
            <div><b>Proceso:</b> <br>{{ report.process ? report.process.name : '-' }}</div>
            <div><b>Área:</b> <br>{{ report.area ? report.area.name : '-' }}</div>
            <div style="padding-top: 20px;"><b-btn @click="showModal" variant="primary"><span class="ion ion-md-eye"></span> Ver plan de acción</b-btn></div>
            <div v-if="existError">
              <b-form-feedback class="d-block" style="padding-bottom: 10px;">
                Hay errores en sus datos
              </b-form-feedback>
            </div>
          </b-col>
        </b-row>

        <b-modal ref="modalPlan" :hideFooter="true" id="modals-default" class="modal-top" size="lg" @hidden="saveActionPlan">
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
              :action-plan="report.actionPlan"/>
          </b-card>
          <br>
          <div class="row float-right pt-12 pr-12y">
            <b-btn variant="primary" @click="hideModal">Cerrar</b-btn>
          </div>
        </b-modal>
      </b-card>
    </div>

    <div class="col-md-12">
      <b-card bg-variant="transparent" border-variant="dark" title="Imágenes" class="mb-3 box-shadow-none">
          <b-row style="padding-bottom: 35px;">
            <b-col>
              <blockquote class="blockquote text-center">
                <p class="mb-0">Imágen 1</p>
              </blockquote>
              <form-image
                :url="`/industrialSecurity/dangerousConditions/report/saveImage`"
                :urlDownload="`/industrialSecurity/dangerousConditions/report/downloadImage/${report.id}/image_1`"
                column="image_1"
                :id="report.id"
                :image="report.image_1"
                :path="report.path_1"
                :old="report.old_1"/>
            </b-col>
          </b-row>
          <b-row style="padding-bottom: 35px;">
            <b-col>
              <blockquote class="blockquote text-center">
                <p class="mb-0">Imágen 2</p>
              </blockquote>
              <form-image
                :url="`/industrialSecurity/dangerousConditions/report/saveImage`"
                :urlDownload="`/industrialSecurity/dangerousConditions/report/downloadImage/${report.id}/image_2`"
                column="image_2"
                :id="report.id"
                :image="report.image_2"
                :path="report.path_2"
                :old="report.old_2"/>
            </b-col>
          </b-row>
          <b-row style="padding-bottom: 35px;">
            <b-col>
              <blockquote class="blockquote text-center">
                <p class="mb-0">Imágen 3</p>
              </blockquote>
              <form-image
                :url="`/industrialSecurity/dangerousConditions/report/saveImage`"
                :urlDownload="`/industrialSecurity/dangerousConditions/report/downloadImage/${report.id}/image_3`"
                column="image_3"
                :id="report.id"
                :image="report.image_3"
                :path="report.path_3"
                :old="report.old_3"/>
            </b-col>
          </b-row>
      </b-card>
    </div>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Atras"}}</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import Alerts from '@/utils/Alerts.js';
import FormImage from '../FormImageComponent.vue';

export default {
  components: {
    PerfectScrollbar,
    ActionPlanComponent,
    FormImage
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
    report: {
      default() {
        return {
          actionPlan: {
            activities: [],
            activitiesRemoved: []
          }
        };
      }
    }
  },
  watch: {
    report() {
      this.loading = false;
      this.form = Form.makeFrom(this.report, this.method, false, false);

      setTimeout(() => {
          this.ready = true
      }, 5000)
    }
  },
  computed: {
    existError() {
			let keys = Object.keys(this.form.errors.errors)
      let result = false
      
      if (keys.length > 0)
      {
        result = true
      }

			return result
		}
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.report, this.method, false, false)
    };
  },
  methods: {
    showModal() {
			this.$refs.modalPlan.show()
		},
		hideModal() {
			this.$refs.modalPlan.hide()
    },
    saveActionPlan()
    {
      if (this.ready)
      {
        this.loading = true;
        
        let data = new FormData();
        data.append('id', this.form.id);
        data.append('actionPlan', JSON.stringify(this.form.actionPlan));

        this.form.resetError()
        this.form
          .submit('/industrialSecurity/dangerousConditions/report/saveQualification', false, data)
          .then(response => {
              this.form.actionPlan.activities = response.data.data.actionPlan.activities
              this.form.actionPlan.activitiesRemoved = response.data.data.actionPlan.activitiesRemoved

            this.loading = false;
            
          })
          .catch(error => {
            this.loading = false;
          });
      }
    }
  }
};
</script>
