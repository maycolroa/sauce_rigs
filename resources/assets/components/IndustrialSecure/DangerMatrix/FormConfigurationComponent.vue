<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-radio :disabled="!auth.can['configurations_c']" :checked="form.danger_matrix_block_old_year" class="col-md-12" v-model="form.danger_matrix_block_old_year" :options="siNo" name="danger_matrix_block_old_year" :error="form.errorsFor('danger_matrix_block_old_year')" label="¿Desea bloquear la edicion de matrices de peligros pertenecientes a años anteriores al actual?">
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
    locationLevels: {
      type: Array,
      default: function() {
        return [];
      }
    },
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    configuration: {
      default() {
        return {
          danger_matrix_block_old_year: '',
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
          window.location = "/industrialsecure/dangermatrix-menu"
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
