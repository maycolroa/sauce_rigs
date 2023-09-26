<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="col-md-12">
      <p class="text-center text-big mb-4">
        Coloque los correos a los cuales desea enviar el reporte del evento.
      </p>
    </div>
    <div>
      <b-form-row>
        <vue-advanced-select class="col-md-12" v-model="form.add_email" name="add_email" :error="form.errorsFor(`add_email`)" label="Correos a los cuales se enviara" placeholder="Selecione correos" :options="[]" :multiple="true" :allowEmpty="true" :taggable="true" :searchable="true" :limit="50">
        </vue-advanced-select>
      </b-form-row>
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String, default: 'POST' },
    isEdit: { type: Boolean, default: true },
    cancelUrl: { type: [String, Object], required: true },
    event: {
      default() {
        return {
            add_email: '',
        };
      }
    }
  },
  watch: {
    event() {
      this.loading = false;
      this.form = Form.makeFrom(this.event, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.event, this.method),
    };
  },
  methods: {
    submit(e) {
      console.log(this.form.add_email)
      //this.form.add_email = form.add_email;
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-accidentswork" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
