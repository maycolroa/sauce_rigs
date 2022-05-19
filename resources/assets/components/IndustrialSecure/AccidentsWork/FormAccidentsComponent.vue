<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off"> 
    <form-wizard ref="wizardFormEvaluation">
      <template slot="step" slot-scope="props">
        <wizard-step :tab="props.tab" @click.native="props.navigateToTab(props.index)" @keyup.enter.native="props.navigateToTab(props.index)" :transition="props.transition" :index="props.index">
        <span slot="title" :class="{'text-danger':props.tab.validationError}" v-html="props.tab.title"></span>
        </wizard-step>
      </template>      
      <tab-content title="Información de la persona que se accidento">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-form-row>
                <vue-radio :disabled="viewOnly" :checked="form.tipo_vinculador_laboral" class="col-md-12" v-model="form.tipo_vinculador_laboral" :options="vinculationLaboral" name="tipo_vinculador_laboral" :error="form.errorsFor('tipo_vinculador_laboral')" label="Tipo de vinculación laboral"></vue-radio>
              </b-form-row>
            </b-card>
          </b-col>
        </b-row>                           
        <b-row v-if="form.tipo_vinculador_laboral == 'Empleador'">
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <vue-ajax-advanced-select class="col-md-12" :disabled="viewOnly" v-model="form.employee_id"  name="employee_id" :label="keywordCheck('employee')" placeholder="Seleccione una opción" :url="employeesDataUrl" :selected-object="form.multiselect_employee" :error="form.errorsFor('employee_id')">
                    </vue-ajax-advanced-select>
              </b-row>
              <b-row>
                <b-col>
                  <information-general 
                  :form="form"
                  :employee-detail="employeeDetail"
                  :employee="form"
                  :view-only="viewOnly"
                  :is-edit="isEdit"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
        <b-row v-if="form.tipo_vinculador_laboral == 'Contratante' || form.tipo_vinculador_laboral == 'Cooperativa de trabaso asociado'">
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <information-employee
                  :form="form"
                  :sexs="sexs"
                  :employee="form"
                  :view-only="viewOnly"
                  :is-edit="isEdit"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <tab-content title="Identificación del empleador, contratante o cooperativa">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <information-company
                  :form="form"
                  :company="form"
                  :view-only="viewOnly"
                  :is-edit="isEdit"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <tab-content title="Información básica">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <accident-infor-basic
                  :form="form"
                  :infor="form"
                  :view-only="viewOnly"
                  :is-edit="isEdit"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <tab-content title="Información sobre el accidente">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <infor-accident
                  :form="form"
                  :infor="form"
                  :view-only="viewOnly"
                  :is-edit="isEdit"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <tab-content title="Descripción del accidente">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <description-accident
                  :form="form"
                  :description="form"
                  :view-only="viewOnly"
                  :is-edit="isEdit"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <tab-content title="Observaciones de la empresa y registro visual">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <observation-files
                  :form="form"
                  :obs="form"
                  :view-only="viewOnly"
                  :is-edit="isEdit"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <tab-content title="Planes de acción">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <action-plan-component
                  :is-edit="isEdit"
                  :view-only="viewOnly"
                  :form="form"
                  :action-plan-states="actionPlanStates"
                  v-model="form.actionPlan"
                  :action-plan="form.actionPlan"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <tab-content title="Participantes de la investigación">
        <b-row>
          <b-col>
            <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
              <b-row>
                <b-col>
                  <person-add
                  :form="form"
                  :persons="form.participants_investigations"
                  :view-only="viewOnly"
                  :is-edit="isEdit"
                  rol='Miembro Investigación'
                  :empty="false"/>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </tab-content>

      <template slot="footer" slot-scope="props">
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>
        <b-btn v-on:click="props.prevTab" :disabled="loading" variant="default">Anterior</b-btn>
        <b-btn v-on:click="props.nextTab" :disabled="loading || props.isLastStep" variant="default">Siguiente</b-btn>                
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </form-wizard>
  </b-form>  
</template>


<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>
<style src="@/vendor/libs/spinkit/spinkit.scss" lang="scss"></style>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import InformationGeneral from '@/components/IndustrialSecure/AccidentsWork/InformationGeneral.vue';
import InformationEmployee from '@/components/IndustrialSecure/AccidentsWork/InformationEmployee.vue';
import InformationCompany from '@/components/IndustrialSecure/AccidentsWork/CompanyInforComponent.vue';
import InforAccident from '@/components/IndustrialSecure/AccidentsWork/InforAccidentComponent.vue';
import AccidentInforBasic from '@/components/IndustrialSecure/AccidentsWork/AccidentInforBasicComponent.vue';
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import DescriptionAccident from '@/components/IndustrialSecure/AccidentsWork/DescriptionAccidentForm.vue';
import ObservationFiles from '@/components/IndustrialSecure/AccidentsWork/ObservationFilesComponent.vue';
import PersonAdd from '@/components/IndustrialSecure/AccidentsWork/PersonAddComponent.vue';

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
    VueTextarea,
    VueFileSimple,
    InformationGeneral,
    InformationEmployee,
    FormWizard,
    TabContent,
    WizardStep,
    InformationCompany,
    AccidentInforBasic,
    ActionPlanComponent,
    DescriptionAccident,
    PersonAdd,
    InforAccident,
    ObservationFiles
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    epsDataUrl: { type: String, default: "" },
    sexs: {
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
    actionPlanStates: {
			type: Array,
			default: function() {
				return [
					{ name:'Pendiente', value:'Pendiente'},
					{ name:'Ejecutada', value:'Ejecutada'}
				];
			}
		},
    accident: {
      default() {
        return {
          employee_id: '',
          tipo_vinculacion_persona: '',
          nombre_persona: '',
          tipo_identificacion_persona: '',
          identificacion_persona: '',
          fecha_nacimiento_persona: '',
          sexo_persona: '',
          direccion_persona: '',
          telefono_persona: '',
          email_persona: '',
          departamento_persona_id: '',
          ciudad_persona_id: '',
          zona_persona: '',
          cargo_persona: '',
          tiempo_ocupacion_habitual_persona: '',
          fecha_ingreso_empresa_persona: '',
          salario_persona: '',
          jornada_trabajo_habitual_persona: '',
          tipo_vinculador_laboral: '',
          razon_social: '',
          nombre_actividad_economica_sede_principal: '',
          tipo_identificacion_sede_principal: '',
          identificacion_sede_principal: '',
          direccion_sede_principal: '',
          telefono_sede_principal: '',
          fax_sede_principal: '',
          email_sede_principal: '',
          departamento_sede_principal_id: '',
          ciudad_sede_principal_id: '',
          zona_sede_principal: '',
          info_sede_principal_misma_centro_trabajo: '',
          nombre_actividad_economica_centro_trabajo: '',
          direccion_centro_trabajo: '',
          telefono_centro_trabajo: '',
          fax_centro_trabajo: '',
          email_centro_trabajo: '',
          departamento_centro_trabajo_id: '',
          ciudad_centro_trabajo_id: '',
          zona_centro_trabajo: '',
          nivel_accidente: '',
          fecha_envio_arl: '',
          fecha_envio_empresa: '',
          coordinador_delegado: '',
          cargo: '',
          employee_eps_id: '',
          employee_arl_id: '',
          employee_afp_id: '',
          tiene_seguro_social: '',
          nombre_seguro_social: '',
          fecha_accidente: '',
          jornada_accidente: '',
          estaba_realizando_labor_habitual: '',
          otra_labor_habitual: '',
          total_tiempo_laborado: '',
          tipo_accidente: '',
          departamento_accidente: '',
          ciudad_accidente: '',
          zona_accidente: '',
          accidente_ocurrio_dentro_empresa: '',
          causo_muerte: '',
          fecha_muerte: '',
          otro_sitio: '',
          otro_mecanismo: '',
          otra_lesion: '',
          descripcion_accidente: '',
          personas_presenciaron_accidente: '',
          nombres_apellidos_responsable_informe: '',
          cargo_responsable_informe: '',
          tipo_identificacion_responsable_informe: '',
          identificacion_responsable_informe: '',
          fecha_diligenciamiento_informe: '',
          observaciones_empresa: '',
          consolidado: '',
          user_id: '',
          site_id: '',
          agent_id: '',
          mechanism_id: '',
          lesions_id: [],
          parts_body: [],
          actionPlan: {
              activities: [],
              activitiesRemoved: []
          },
          persons: {
             persons: [],
             delete: []
          },
          participants_investigations: {
             persons: [{
              name: '',
              position: '',
              document: '',
              type_document: '',
              rol:'Miembro Investigación'
          }],
             delete: []
          },
          files: []
        };
      }
    }
  },
  watch: {
    accident() {
      this.form = Form.makeFrom(this.accident, this.method);
    },
    'form.employee_id' () {
      this.updateDetails(`/administration/employee/${this.form.employee_id}`, 'employeeDetail')
    }
  },
  computed: {
  },
  mounted() {
    setTimeout(() => {
      this.$refs.wizardFormEvaluation.activateAll();
      if (this.form.employee_id)
      {
      this.updateDetails(`/administration/employee/${this.form.employee_id}`, 'employeeDetail')
      } 
    }, 4000)       
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.accident, this.method),
      employeeDetail: [],
      employeesDataUrl: "/selects/employees",
      typeVinculation: [
        {text: 'Planta', value: 'Planta'},
        {text: 'Misión', value: 'Misión'},
        {text: 'Cooperado', value: 'Cooperado'},
        {text: 'Estudiante o Aprendiz', value: 'Estudiante o Aprendiz'},
        {text: 'Independiente', value: 'Independiente'}
      ],
      vinculationLaboral: [
        {text: 'Empleador', value: 'Empleador'},
        {text: 'Contratante', value: 'Contratante'},
        {text: 'Cooperativa de trabaso asociado', value: 'Cooperativa de trabaso asociado'}
      ],
      zones: [
         {text: 'Urbana', value: 'Urbana'},
         {text: 'Rural', value: 'Rural'}
      ],
      personLinkingTypes: [
        {text: 'Planta', value: 'Planta'},
        {text: 'Misión', value: 'Misión'},
        {text: 'Cooperado', value: 'Cooperado'},
        {text: 'Estudiante o Aprendiz', value: 'Estudiante o Aprendiz'},
        {text: 'Independiente', value: 'Independiente'}
      ],
      typesDocuments: [
        {text: 'NI', value: 'NI'},
        {text: 'CC', value: 'CC'},
        {text: 'CE', value: 'CE'},
        {text: 'NU', value: 'NU'},
        {text: 'PA', value: 'PA'},
      ],
      shifts: [
        {text: 'Diurna', value: 'Diurna'},
        {text: 'Nocturna', value: 'Nocturna'},
        {text: 'Mixto', value: 'Mixto'},
        {text: 'Turnos', value: 'Turnos'}
      ],
      workingDayType: [
        {text: 'Normal', value: 'Normal'},
        {text: 'Extra', value: 'Extra'}
      ],
      accidentTypes: [
        {text: 'Violencia', value: 'Violencia'},
        {text: 'Tránsito', value: 'Tránsito'},
        {text: 'Deportivo', value: 'Deportivo'},
        {text: 'Recreativo o cultural', value: 'Recreativo o cultural'},
        {text: 'Propios del trabajo', value: 'Propios del trabajo'}
      ],
      interventionControlTypes: [
        {text: 'Fuente', value: 'Fuente'},
        {text: 'Medio', value: 'Medio'},
        {text: 'Persona', value: 'Persona'}
      ]
    };
  },
  methods: {
    submit(e) {

      this.loading = true;

      /*this.form.clearFilesBinary();

      this.form.files.forEach((file, keyFile) => {
        this.form.addFileBinary(`${keyFile}`, file.file)
      });*/
      
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-accidentswork" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    updateDetails(url, key)
    {
      this.isLoading = true;
      axios.get(url)
      .then(response => {
          this[key] = response.data.data;
          this.isLoading = false;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
    },
    formatDate(param)
    {
      let date = ''

      if (param)
        date = new Date(param).toLocaleDateString()

      return date
    },
    pushRemoveFile(value)
    {
      this.form.delete.files.push(value)
    },
  }
};
</script>
