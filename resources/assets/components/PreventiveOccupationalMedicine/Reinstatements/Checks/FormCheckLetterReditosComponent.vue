<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input class="col-md-12" v-model="form.from" label="De" type="text" name="from" :error="form.errorsFor('from')" placeholder=""></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-input class="col-md-12" v-model="form.to" label="Para" type="text" name="to" :error="form.errorsFor('to')" placeholder=""></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-input class="col-md-12" v-model="form.subject" label="Asunto" type="text" name="subject" :error="form.errorsFor('subject')" placeholder=""></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-input class="col-md-12" v-model="form.firm" label="Firma" type="text" name="firm" :error="form.errorsFor('firm')" placeholder=""></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-checkbox style="padding-top: 20px;" class="col-md-12" v-model="form.selectedRecomendations" label="Recomendaciones" name="selectedRecomendations" :options="['Laborales', 'Extralaborales']" :vertical="false"></vue-checkbox>
    </b-form-row>


    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" variant="primary" v-if="!viewOnly">Generar PDF</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";
import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";

export default {
  components: {
    VueInput,
    VueCheckbox
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    letter: {
      default() {
        return {
            from: '',
            to: '',
            subject: '',
            firm: '',
            selectedRecomendations: []
        };
      }
    }
  },
  watch: {
    letter() {
      this.loading = false;
      this.form = Form.makeFrom(this.letter, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.letter, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

      window.open(`/biologicalmonitoring/reinstatements/check/generateLetter?check_id=${this.form.id}&from=${this.form.from}&to=${this.form.to}&subject=${this.form.subject}&firm=${this.form.firm}&selectedRecomendations=${this.form.selectedRecomendations.join(",")}`, '_blank')

      this.$router.push({ name: "reinstatements-checks" });
    }
  }
};
</script>
