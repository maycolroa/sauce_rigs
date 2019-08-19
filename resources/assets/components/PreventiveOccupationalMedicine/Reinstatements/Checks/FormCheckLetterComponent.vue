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

export default {
  components: {
    VueInput
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
            subject: ''
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

      window.open(`/biologicalmonitoring/reinstatements/check/generateLetter?check_id=${this.form.id}&from=${this.form.from}&to=${this.form.to}&subject=${this.form.subject}`, '_blank')

      this.$router.push({ name: "reinstatements-checks" });
    }
  }
};
</script>
