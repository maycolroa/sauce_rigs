<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-advanced-select-group v-model="form.module_id" class="col-md-12" :options="modules" :limit="1000" :searchable="true" name="module_id" label="Aplicación \ Módulo" placeholder="Seleccione un modulo" text-block="Módulo al que sera redireccionado al iniciar sesión" :error="form.errorsFor('module_id')" :selected-object="form.multiselect_module">
          </vue-advanced-select-group>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAdvancedSelectGroup from "@/components/Inputs/VueAdvancedSelectGroup.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelectGroup
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    modules: {
      type: Array,
      default: function() {
        return [];
      }
    },
    user: {
      default() {
        return {
            module_id: ''
        };
      }
    }
  },
  watch: {
    user () {
      this.loading = false;
      this.form = Form.makeFrom(this.user, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.user, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ path: '/' });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
