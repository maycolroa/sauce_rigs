<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="col-md-12">
      <p class="text-center text-big mb-4">
        ¿Está seguro que desea cambiar el estado del elemento seleccionado?
      </p>
    </div>
    <div class="col-md-12">
      <center>
        <b-form-row v-if="form.state == 'ABIERTO'">
          <vue-datepicker class="col-md-6 offset-md-3" v-model="form.deadline" label="Fecha de cierre" :full-month-name="true" :error="form.errorsFor('deadline')" name="deadline" text-block="Campo opcional">
                </vue-datepicker>
        </b-form-row>
        <b-form-row v-if="form.state == 'ABIERTO'">
          <vue-ajax-advanced-select-tag-unic class="col-md-12" v-model="form.motive_close" name="motive_close" :error="form.errorsFor('motive_close')" label="Motivo del cierre" placeholder="Seleccione una opción" :url="tagsMotiveDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
          </vue-ajax-advanced-select-tag-unic>
        </b-form-row>
      </center>
    </div>  

    <div class="col-md-12 pt-10 pr-10">
      <center>
        <b-btn variant="default" :to="cancelUrl">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Aceptar</b-btn>
      </center>
    </div>
  </b-form>
</template>

<script>
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueDatepicker,
    VueAjaxAdvancedSelectTagUnic
  },
  props: {
    url: { type: String },
    method: { type: String, default: 'PUT' },
    isEdit: { type: Boolean, default: true },
    cancelUrl: { type: [String, Object], required: true },
    check: {
      default() {
        return {
            state: '',
            deadline: ''
        };
      }
    }
  },
  watch: {
    check() {
      this.loading = false;
      this.form = Form.makeFrom(this.check, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.check, this.method),
      tagsMotiveDataUrl: '/selects/tagsReincMotive',
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;

            Object.assign(this.$data, this.$options.data.apply(this))
            //this.closeEvent()
            this.$router.push({ name: "reinstatements-checks" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    closeEvent() {
      this.$emit("closeModal")
    }
  }
};
</script>
