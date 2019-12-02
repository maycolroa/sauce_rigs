<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                  <b-form-row>

                    <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.observation" label="Observación:" type="text" name="observation" :error="form.errorsFor('observation')" placeholder="Observación:"></vue-input>
                    
                    <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.regional_id" :error="form.errorsFor('regional_id')" :selected-object="form.multiselect_regional" name="regional_id" :label="keywordCheck('regional')" placeholder="Seleccione una opción" :url="regionalsDataUrl">
                    </vue-ajax-advanced-select>

                    <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.condition_id" :error="form.errorsFor('condition_id')" :selected-object="form.multiselect_condition" name="condition_id" label="Condición" placeholder="`Seleccione la condición`" :url="conditionsDataUrl">
                    </vue-ajax-advanced-select>

                    <vue-ajax-advanced-select 
                    :disabled="viewOnly || !form.regional_id" class="col-md-6" 
                    v-model="form.headquarter_id" 
                    :error="form.errorsFor('headquarter_id')" 
                    :selected-object="form.multiselect_sede" name="headquarter_id" :label="keywordCheck('headquarter')"
                    placeholder="Seleccione una opción" 
                    :url="headquartersDataUrl" 
                    :parameters="{regional: form.regional_id }" 
                    :emptyAll="empty.headquarter" 
                    @updateEmpty="updateEmptyKey('headquarter')">
                    </vue-ajax-advanced-select>
                    
                  </b-form-row>
                  <b-form-row>
                    <vue-input :disabled="true" class="col-md-6" v-model="form.user_name" label="Usuario que reporta:" type="text" name="user_name" :error="form.errorsFor('user_name')" placeholder="Usuario que reporta:"></vue-input>

                    <vue-ajax-advanced-select :disabled="viewOnly || !form.headquarter_id" class="col-md-6" v-model="form.process_id" :error="form.errorsFor('process_id')" :selected-object="form.multiselect_proceso" name="process_id" :label="keywordCheck('process')" placeholder="Seleccione una opción" :url="processesDataUrl" :parameters="{headquarter: form.headquarter_id }" :emptyAll="empty.process" @updateEmpty="updateEmptyKey('process')">
                    </vue-ajax-advanced-select>
                    
                  </b-form-row>
                  <b-form-row>
                  
                    <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.rate" :error="form.errorsFor('rate')" :multiple="false" :options="rates" :hide-selected="false" name="rate" label="Severidad" placeholder="`Seleccione la severidad`">
                    </vue-advanced-select>

                    <vue-ajax-advanced-select :disabled="viewOnly || !form.process_id" class="col-md-6" v-model="form.area_id" :error="form.errorsFor('area_id')" :selected-object="form.multiselect_area" name="area_id" :label="keywordCheck('area')" placeholder="Seleccione una opción" :url="areasDataUrl" :parameters="{process: form.process_id, headquarter: form.headquarter_id }" :emptyAll="empty.area" @updateEmpty="updateEmptyKey('area')">
                    </vue-ajax-advanced-select>
                    
                  </b-form-row>
                </b-card>
                <b-card border-variant="primary" class="mb-3 box-shadow-none">
                  <action-plan-component
                                :is-edit="!viewOnly"
                                :view-only="viewOnly"
                                :form="form"
                                :action-plan-states="actionPlanStates"
                                v-model="form.actionPlan"
                                :action-plan="report.actionPlan"/>
                </b-card>
                
                <b-card border-variant="primary" title="Imagenes:" class="mb-3 box-shadow-none">
                  <div class="row">
                    <div class="col-md-4">
                    <form-image
                        :url="`/industrialSecurity/inspections/conditionsReports/image`"
                        method="POST"
                        :is-edit="isEdit"
                        :view-only="viewOnly"
                        column="image_1"
                        :report-id="report.id"
                        :image="report.image_1"
                        :path="report.path_1"
                        :old="report.old_1"
                        />
                  </div>
                  <div class="col-md-4">
                    <form-image
                        :url="`/industrialSecurity/inspections/conditionsReports/image`"
                        method="POST"
                        :is-edit="isEdit"
                        :view-only="viewOnly"
                        column="image_2"
                        :report-id="report.id"
                        :image="report.image_2"
                        :path="report.path_2"
                        :old="report.old_2"
                        />
                  </div>
                  <div class="col-md-4">      
                    <form-image
                        :url="`/industrialSecurity/inspections/conditionsReports/image`"
                        method="POST"
                        :is-edit="isEdit"
                        column="image_3"
                        :view-only="viewOnly"
                        :report-id="report.id"
                        :image="report.image_3"
                        :path="report.path_3"
                        :old="report.old_3"
                        />
                  </div>
                 </div>
                </b-card>
				        <div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>


<script>

import VueInput from "@/components/Inputs/VueInput.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import FormImage from '@/components/IndustrialSecure/Inspections/FormImageComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  components: {
		VueInput,
    ActionPlanComponent,
    VueAjaxAdvancedSelect,
    VueAdvancedSelect,
    VueFileSimple,
    FormImage,
    Loading
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
    conditionsDataUrl: { type: String, default: "" },
    processesDataUrl: { type: String, default: "" },
    disableWacthSelectInCreated: { type: Boolean, default: false},
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
            condition: '',
            user_name: '',
            regional_id: '',
            headquarter_id: '',
            area_id: '',
            process_id: '',
            rate: '',
            image_1: '',
            image_2: '',
            image_3: '',
            actionPlan: {
              activities: [],
              activitiesRemoved: []
            },
        };
      }
    }
  },
	watch: {
		report() {
			this.loading = false;
			this.form = Form.makeFrom(this.report, this.method);
		},
    'form.regional_id'() {
      this.emptySelect('process_id', 'process')
      this.emptySelect('area_id', 'area')
      this.emptySelect('headquarter_id', 'headquarter')
    },
    'form.headquarter_id'() {
      this.emptySelect('process_id', 'process')
      this.emptySelect('area_id', 'area')
    },
    'form.process_id'() {
      this.emptySelect('area_id', 'area')
    },
    'form.area_id'() {
      if (this.disableWacth)
        this.disableWacth = false
    }
	},
	data() {
		return {
			loading: this.isEdit,
      form: Form.makeFrom(this.report, this.method),
      empty: {
        headquarter: false,
        area: false,
        process: false,
      },
      disableWacth: this.disableWacthSelectInCreated
		};
  },
	methods: {

    updateEmptyKey(keyEmpty)
    {
      this.empty[keyEmpty]  = false
    },
    emptySelect(keySelect, keyEmpty)
    {
      if (this.form[keySelect] !== '' && !this.disableWacth)
      {
        this.empty[keyEmpty] = true
        this.form[keySelect] = ''
      }
    },
    submit(e) {
        this.loading = true;
        this.form
          .submit(e.target.action)
          .then(response => {
            this.loading = false;
            this.$router.push({ name: "inspections-conditionsReports" });
          })
          .catch(error => {
            this.loading = false;
          });
    }
	}
}
</script>
