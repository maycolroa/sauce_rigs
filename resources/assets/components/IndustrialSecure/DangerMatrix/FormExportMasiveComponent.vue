<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-body>
        <b-form-row>
          <vue-input class="col-md-6" v-model="form.source" label="Fuente" name="source" type="text" placeholder="Fuente" :error="form.errorsFor('source')"></vue-input>

          <vue-textarea class="col-md-6" v-model="form.observations" label="Observaciones" name="observations" row="3" placeholder="Observaciones" :error="form.errorsFor('observations')"></vue-textarea>
        </b-form-row>
        <b-form-row>
          <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.danger_matrix_id" name="danger_matrix_id" :error="form.errorsFor('danger_matrix_id')" label="Matriz de peligros" placeholder="Seleccione las matrices de peligro" :url="dangerMatrixDataUrl" :multiple="true" :allowEmpty="true">
                    </vue-ajax-advanced-select>
        </b-form-row>
      </b-card-body>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Exportar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Alerts from '@/utils/Alerts.js';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    VueCheckboxSimple,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    user: {
      default() {
        return {
          observations: '',
          source: '',
          danger_matrix_id: ''
        };
      }
    }
  },
  watch: {
    user() {
      this.loading = false;
      this.form = Form.makeFrom(this.user, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.user, this.method),
      dangerMatrixDataUrl: '/selects/dmDangerMatrix'
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-dangermatrix" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
}
</script>
