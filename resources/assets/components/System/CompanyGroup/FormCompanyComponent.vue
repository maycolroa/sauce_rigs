<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-radio :disabled="viewOnly" :checked="form.receive_report" class="col-md-12" v-model="form.receive_report" :options="siNo" name="receive_report" :error="form.errorsFor('receive_report')" label="¿Desea enviar un reporte sobre la actividad de sus compañias?">
        </vue-radio>
    </b-form-row>
    <b-form-row v-if="form.receive_report == 'SI'">
      <vue-advanced-select class="col-md-12" :disabled="viewOnly" v-model="form.emails" name="emails" :error="form.errorsFor(`emails`)" label="Emails que seran notificados" placeholder="Selecione emails" :options="[]" :multiple="true" :allowEmpty="true" :taggable="true" :searchable="true" :limit="50">
      </vue-advanced-select>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueInput,
    VueRadio
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    companiesGroup: {
      default() {
        return {
          name: '',
          receive_report: '',
          emails: []
        };
      }
    },
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
  },
  watch: {
    companiesGroup() {
      this.loading = false;
      this.form = Form.makeFrom(this.companiesGroup, this.method);
    }
  },
  /*computed: {
    usersComputed() {
      let data = [];

      if (this.usersOptions.length > 0)
      {
        let ids = this.form.users.map((f) => {
          return f.user_id;
        });

        data = this.usersOptions.filter((f) => {
          return !ids.includes(f.value);
        });
      }

      return data;
    }
  },*/
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.companiesGroup, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "system-companygroup" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
