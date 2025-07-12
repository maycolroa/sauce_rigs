<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <template v-for="(column, index) in table.columns.columns">
      <b-form-row>
        <vue-input :disabled="viewOnly" class="col-md-12" v-model="form[column]" :label="column" type="text" :name="column" :error="form.errorsFor(column)" :placeholder="column"/>
      </b-form-row>
    </template>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
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
    table: { type: Object, required: true },
    record: {
      default() {
        return {
          name: '',
          columnas: [],
          delete: []
        };
      }
    }
  },
  watch: {
    record() {
      this.loading = false;
      this.form = Form.makeFrom(this.record, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.record, this.method),
    };
  },
  methods: {
    submit(e) {

      this.loading = true;

      let data = new FormData();
      data.append('table_id', this.table.id);
      data.append('id', this.form.id ? this.form.id : '');

      this.table.columns.columns.forEach(columna => {
          data.append(columna, this.form[columna] ? this.form[columna] : '');
      });
       
      this.form.resetError()
      this.form
      .submit(this.url, false, data)
      .then(response => {
          this.loading = false;
          this.$router.push({ name: "absenteeism-tables-records" });
      })
      .catch(error => {
          this.loading = false;
      });
    }
  }
};
</script>
