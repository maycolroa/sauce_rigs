<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.location_level_form_table_inspectiona" class="col-md-12" v-model="form.location_level_form_table_inspectiona" :options="locationLevels" name="location_level_form_table_inspectiona" :error="form.errorsFor('location_level_form')" label="Nivel localización en tabla de inspecciones planeadas">
        </vue-radio>
    </b-form-row>  

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.mandatory_action_plan_inspections" class="col-md-12" v-model="form.mandatory_action_plan_inspections" :options="siNo" name="mandatory_action_plan_inspections" :error="form.errorsFor('mandatory_action_plan_inspections')" label="¿Se debe pedir obligatoriamente los planes de accion en items calificados como 'Parcial' o 'No Cumple'?">
        </vue-radio>
    </b-form-row>  

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.mandatory_level_risk_inspections" class="col-md-12" v-model="form.mandatory_level_risk_inspections" :options="siNo" name="mandatory_level_risk_inspections" :error="form.errorsFor('mandatory_level_risk_inspections')" label="¿Se debe pedir obligatoriamente el campo nivel de riesgo en los items calificados?">
        </vue-radio>
    </b-form-row> 

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.filter_inspections" class="col-md-12" v-model="form.filter_inspections" :options="siNo" name="filter_inspections" :error="form.errorsFor('filter_inspections')" label="¿Desea activar el filtrado de visualizacion de inspecciones por parte de usuarios segun nivel de localizacion?">
        </vue-radio>
    </b-form-row>

    <b-form-row v-if="form.filter_inspections == 'SI'">
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.location_level_form_user_inspection_filter" class="col-md-12" v-model="form.location_level_form_user_inspection_filter" :options="locationLevels" name="location_level_form_user_inspection_filter" :error="form.errorsFor('location_level_form_user_inspection_filter')" label="Nivel localización en tabla de inspecciones planeadas para los usuarios">
        </vue-radio>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading || (!auth.can['configurations_c'])" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String },
    locationLevels: {
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
    configuration: {
      default() {
        return {
          location_level_form_table_inspectiona: '',
          mandatory_action_plan_inspections: '',
          mandatory_level_risk_inspections: '',
          filter_inspections: '',
          location_level_form_user_inspection_filter: ''
        };
      }
    }
  },
  watch: {
    configuration() {
      this.loading = false;
      this.form = Form.makeFrom(this.configuration, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.configuration, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
