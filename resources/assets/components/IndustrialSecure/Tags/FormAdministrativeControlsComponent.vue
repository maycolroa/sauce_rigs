<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <b-card>
      <div>
        <template>
          <vue-table
            configName="industrialsecure-tags-administrative-control-search"
            :params="{ keyword: form.name}"
          ></vue-table>
        </template>
      </div>
    </b-card>

    <b-form-row>
      <vue-radio :disabled="viewOnly" :checked="form.rewrite" class="col-md-6" v-model="form.rewrite" :options="siNo" name="rewrite" :error="form.errorsFor('rewrite')" label="¿Desea sobreescribir el valor en todas las matrices en las cuales se encuentra?">
                    </vue-radio>
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
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    tag: {
      default() {
        return {
            name: '',
            rewrite: ''
        };
      }
    }
  },
  watch: {
    tag() {
      this.loading = false;
      this.form = Form.makeFrom(this.tag, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.tag, this.method),
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;

          this.$router.push({ name: "industrialsecure-dangermatrix-tags-administrative-controls" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
