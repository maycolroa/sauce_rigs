<template>
  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <p><b>Se ha enviado un código de 6 caracteres a su correo electronico, por favor ingreselo en el campo disponible para completar su inicio de sesión. Tome en consideración que se tomaran en cuenta las mayusculas y minusculas incluidas en el código.</b></p>
    </b-form-row>

    <b-form-row>
      <vue-input class="col-md-6 offset-md-3" v-model="form.code_validation" label="Código" type="text" name="code_validation" :error="form.errorsFor('code_validation')" placeholder="Código"></vue-input>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" variant="primary">Aceptar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import Form from "@/utils/Form.js";
import VueInput from "@/components/Inputs/VueInput.vue";

export default {
  components: {
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    info: {
      default(){
        return {
          code_validation: '',
          user_id: ''
        }
      }
    }
  },
  data() {
    return {
      form: Form.makeFrom(this.info, this.method),
    };
  },
  methods: {
    submit(e) {
      this.form.user_id = this.auth.user_auth.id;
      this.form
        .submit(e.target.action)
        .then(response => {
          window.location =  "/";
        })
        .catch(error => {});
    }
  }
};
</script>
