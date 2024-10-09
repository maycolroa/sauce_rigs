<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="col-md-12">
      <center>
        <b-form-row>         
          <vue-radio class="col-md-12" v-model="form.state" :options="states" name="state" :error="form.errorsFor('state')" label="Estado" :checked="form.state"></vue-radio>
        </b-form-row>

        <b-form-row v-if="form.state == 'Rechazado'">
          <vue-input class="col-md-12" v-model="form.motive_rejected" label="Motivo de rechazo" type="text" name="motive_rejected" :error="form.errorsFor('motive_rejected')" placeholder="Motivo"/>
        </b-form-row>
      </center>
    </div> 
    <div class="col-md-12 pt-10 pr-10">
      <br>
      <center>
        <b-btn variant="default" @click="regresar()" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
      </center>
    </div>
  </b-form>
</template>

<script>
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import VueRadio from "@/components/Inputs/VueRadio.vue";

export default {
  components: {
    VueDatepicker,
    VueAjaxAdvancedSelectTagUnic,
    VueInput,
    VueFileSimple,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String, default: 'POST' },
    isEdit: { type: Boolean, default: true },
    cancelUrl: { type: [String, Object], required: true },
    inform: {
      default() {
        return {
            state: '',
            motive_rejected: ''
        };
      }
    }
  },
  watch: {
    inform() {
      this.loading = false;
      this.form = Form.makeFrom(this.inform, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.employee, this.method),
      states: [
        {text: 'Aprobado', value: 'Aprobado'},
        {text: 'Rechazado', value: 'Rechazado'}
      ],
    };
  },
  methods: {
    regresar() {
          this.$router.back();
    },
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.back();
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
