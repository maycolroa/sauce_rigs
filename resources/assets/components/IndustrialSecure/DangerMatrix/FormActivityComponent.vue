<template>
    <b-form autocomplete="off">
      <b-form-row>
        <vue-ajax-advanced-select @selectedName="emitActivityName" :disabled="viewOnly" class="col-md-6" v-model="activity.activity_id" :selected-object="activity.multiselect_activity" name="activity_id" :error="form.errorsFor('activities.'+indexActivity+'.activity_id')" label="Actividad" placeholder="Seleccione la actividad" :url="activitiesDataUrl">
          </vue-ajax-advanced-select>
        <vue-radio :disabled="viewOnly" :checked="activity.type_activity" class="col-md-6" v-model="activity.type_activity" :options="typeActivities" name="type_activity" :error="form.errorsFor('activities.'+indexActivity+'.type_activity')" label="Tipo de actividad">
          </vue-radio>
      </b-form-row>
      <b-form-row>
        <div class="col-md-12" v-if="!viewOnly">
          <div class="float-right" style="padding-top: 10px;">
            <b-btn variant="primary" @click.prevent="addDanger()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Peligro</b-btn>
          </div>
        </div>
      </b-form-row>
      <b-form-row v-if="activity.dangers.length > 0">
        <vue-input class="col-md-12" v-model="search" label="Buscar Peligro" type="text" name="search" placeholder="Buscar Peligro" append='<span class="fas fa-search"></span>'></vue-input>
      </b-form-row>
      <b-form-row style="padding-top: 15px;">
        <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 600px; padding-right: 15px; width: 100%;">
          <template v-for="(danger, index) in activity.dangers">
            <b-card no-body class="mb-2 border-secondary" :key="danger.key" style="width: 100%;" v-show="showDander(danger.danger.name)">
              <b-card-header class="bg-secondary">
                <b-row>
                  <b-col cols="10" class="d-flex justify-content-between text-white"> {{ danger.danger.name ? danger.danger.name : 'Nuevo Peligro '+(index + 1) }}</b-col>
                  <b-col cols="2">
                    <div class="float-right">
                      <b-button-group>
                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + danger.key+'-1'" variant="link">
                          <span class="collapse-icon"></span>
                        </b-btn>
                        <b-btn @click.prevent="removeDanger(index)" 
                          v-if="activity.dangers.length > 1 && !viewOnly"
                          size="sm" 
                          variant="secondary icon-btn borderless"
                          v-b-tooltip.top title="Eliminar Peligro">
                          <span class="ion ion-md-close-circle"></span>
                        </b-btn>
                      </b-button-group>
                    </div>
                  </b-col>
                </b-row>
              </b-card-header>
              <b-collapse :id="`accordion${danger.key}-1`" visible :accordion="`accordion${danger.key}`">
                <b-card-body>
                  <form-danger-component
                    :is-edit="isEdit"
                    :view-only="viewOnly"
                    :danger="danger"
                    v-model="activity.dangers[index]"
                    :danger-generated="dangerGenerated"
                    :si-no="siNo"
                    :qualifications="qualifications"
                    :form="form"
                    :index-activity="indexActivity"
                    :index-danger="index"
                    @dangerName="updateDangerNamePanel"
                  />
                </b-card-body>
              </b-collapse>
            </b-card>
          </template>
        </perfect-scrollbar>
      </b-form-row>
    </b-form>
</template>

<script>
import FormDangerComponent from '@/components/IndustrialSecure/DangerMatrix/FormDangerComponent.vue';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import VueInput from "@/components/Inputs/VueInput.vue";

export default {
  components: {
    FormDangerComponent,
    VueRadio,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueInput
  },
  props: {
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    form: { type: Object, required: true },
    indexActivity: { type: Number, required: true },
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
    activity: {
      default() {
        return {
            key: new Date().getTime(),
            id: '',
            activity_id: '',
            type_activity: '',
            dangers: [
              {
                key: new Date().getTime(),
                id: '',
                dm_activity_id: '',
                danger_id: '',
                danger_generated: '',
                possible_consequences_danger: '',
                generating_source: '',
                collaborators_quantity: '',
                esd_quantity: '',
                visitor_quantity: '',
                student_quantity: '',
                esc_quantity: '',
                existing_controls_engineering_controls: '',
                existing_controls_substitution: '',
                existing_controls_warning_signage: '',
                existing_controls_administrative_controls: '',
                existing_controls_epp: '',
                legal_requirements: '',
                quality_policies: '',
                objectives_goals: '',
                risk_acceptability: '',
                intervention_measures_elimination: '',
                intervention_measures_substitution: '',
                intervention_measures_engineering_controls: '',
                intervention_measures_warning_signage: '',
                intervention_measures_administrative_controls: '',
                intervention_measures_epp: '',
                qualifications: '',
                danger: {
                  name: ''
                }
              }
            ],
            dangersRemoved: [],
            activity: {
              name: ''
            }
        };
      }
    }
  },
  watch: {
    activity() {
      this.loading = false;
      this.$emit('input', this.activity);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      activitiesDataUrl: '/selects/dmActivities',
      search: ''
    };
  },
  computed: {
    
  },
  methods: {
    addDanger() {
      this.activity.dangers.push({
        key: new Date().getTime(),
        id: '',
        dm_activity_id: '',
        danger_id: '',
        danger_generated: '',
        possible_consequences_danger: '',
        generating_source: '',
        collaborators_quantity: '',
        esd_quantity: '',
        visitor_quantity: '',
        student_quantity: '',
        esc_quantity: '',
        existing_controls_engineering_controls: '',
        existing_controls_substitution: '',
        existing_controls_warning_signage: '',
        existing_controls_administrative_controls: '',
        existing_controls_epp: '',
        legal_requirements: '',
        quality_policies: '',
        objectives_goals: '',
        risk_acceptability: '',
        intervention_measures_elimination: '',
        intervention_measures_substitution: '',
        intervention_measures_engineering_controls: '',
        intervention_measures_warning_signage: '',
        intervention_measures_administrative_controls: '',
        intervention_measures_epp: '',
        qualifications: '',
        danger: {
          name: ''
        }
      })
    },
    removeDanger(index) {
      if(this.activity.dangers[index].id != '')
        this.activity.dangersRemoved.push(this.activity.dangers[index])
      this.activity.dangers.splice(index, 1)
    },
    emitActivityName(value) {
      this.$emit('activityName', value, this.indexActivity)
    },
    updateDangerNamePanel(values, index) {
      this.activity.dangers[index].danger.name = values
    },
    showDander(danger) {
      if (this.search != '')
      {
        return danger.toLowerCase().indexOf(this.search.toLowerCase()) !== -1 ? true : false
      }
      else
        return true;
    }
  }
};
</script>
