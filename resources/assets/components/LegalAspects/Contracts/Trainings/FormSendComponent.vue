<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="col-md-12">
      <p class="text-center text-big mb-4">
        ¿De que forma quiere enviar la capacitación?
      </p>
    </div>
    <div class="col-md-12">
      <center>
        <b-form-row class="text-center text-big mb-4">
          <vue-radio class="col-md-6 offset-md-3" v-model="form.type_send" :options="siNo" name="type_send" :error="form.errorsFor('type_send')">
              </vue-radio>
        </b-form-row>

        <b-form-row class="text-center text-big mb-4" v-if="form.type_send == 'Especifica'">
          <vue-ajax-advanced-select class="col-md-12" v-model="form.contract_id" name="contract_id" label="Contratista" placeholder="Seleccione la contratista" :url="contractDataUrl" :multiple="true" :error="form.errorsFor('contract_id')"></vue-ajax-advanced-select>
        </b-form-row>
      </center>
    </div>  

    <div class="col-md-12 pt-10 pr-10">
      <br>
      <center>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
  components: {
    VueDatepicker,
    VueAjaxAdvancedSelectTagUnic,
    VueInput,
    VueFileSimple,
    VueRadio,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String, default: 'POST' },
    isEdit: { type: Boolean, default: true },
    cancelUrl: { type: [String, Object], required: true },
    send: {
      default() {
        return {
            type_send: '',
            contract_id: ''
        };
      }
    }
  },
  watch: {
    send() {
      this.loading = false;
      this.form = Form.makeFrom(this.send, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.send, this.method),
      siNo: [
        {text: 'General', value: 'General'},
        {text: 'Especifica', value: 'Especifica'}
      ],
      contractDataUrl: '/selects/contractors',
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-contracts-trainings-virtual" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    refresh() {
        this.$router.push({ name: "legalaspects-contracts-trainings-virtual" });
    },
  }
};
</script>
