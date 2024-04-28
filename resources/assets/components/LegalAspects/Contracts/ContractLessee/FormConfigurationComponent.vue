<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.contracts_delete_file_upload" class="col-md-12" v-model="form.contracts_delete_file_upload" :options="siNo" name="contracts_delete_file_upload" :error="form.errorsFor('contracts_delete_file_upload')" label="¿Permitir eliminar archivos aprobados o rechazados?">
        </vue-radio>
    </b-form-row> 

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.contracts_use_proyect" class="col-md-12" v-model="form.contracts_use_proyect" :options="siNo" name="contracts_use_proyect" :error="form.errorsFor('contracts_use_proyect')" label="¿Desea usar proyectos?">
        </vue-radio>
    </b-form-row> 

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading || (!auth.can['configurations_c'])" variant="primary">Guardar</b-btn>
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
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    configuration: {
      default() {
        return {
          contracts_delete_file_upload: '',
          contracts_use_proyect: ''
        };
      }
    }
  },
  watch: {
    configuration() {
      this.loading = false;
      this.form = Form.makeFrom(this.configuration, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.configuration, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          window.location = "/legalaspects/contracts"
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
