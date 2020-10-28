<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-form-row>
      <vue-input class="col-md-12" v-model="form.old_password" label="Contraseña Anterior" type="password" name="old_password" :error="form.errorsFor('old_password')" placeholder="Contraseña Anterior"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-input class="col-md-12" v-model="form.password" help-text="La contraseña debe contener mínimo 8 caracteres entre los cuales debe haber al menos un número y un caracter especial (@$!%?&*._-)." label="Nueva Contraseña" type="password" name="password" :error="form.errorsFor('password')" placeholder="Nueva Contraseña"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-input class="col-md-12" v-model="form.password_confirmation" label="Confirmar Contraseña" type="password" name="password_confirmation" :error="form.errorsFor('password_confirmation')" placeholder="Confirmar Contraseña"></vue-input>
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
    user: {
      default() {
        return {
            old_password: '',
            password: '',
            password_confirmation: ''
        };
      }
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
