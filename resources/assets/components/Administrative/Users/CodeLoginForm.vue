<template>
  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <p><b>Se ha enviado un código de 6 caracteres a su correo electronico, por favor ingreselo en el campo disponible para completar su inicio de sesión. Tome en consideración que se tomaran en cuenta las mayusculas y minusculas incluidas en el código.</b></p>
    </b-form-row>

    <b-form-row>
      <vue-input class="col-md-6 offset-md-3" v-model="form.code_validation" label="Código" type="text" name="code_validation" :error="form.errorsFor('code_validation')" placeholder="Código"></vue-input>
    </b-form-row>

    <div v-show="getCode">
      <template>
        <p>Si el codigo tarda mucho en llego o no le llega presione el boton para acceder al código generado para completar su inicio de sesión.</p>
        <br><br>
        <b-btn variant="primary" @click.prevent="showCode()">Mostrar Código</b-btn>
      </template>
    </div>
    <br><br>
    <div v-show="viewCode">
      <template>
        <p style="color: red;">{{ auth.user_auth.code_login }}</p>
      </template>
    </div>

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
import Alerts from '@/utils/Alerts.js';

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
      viewCode: false,
      getCode: false
    };
  },
  mounted() {
    setTimeout(() => {
      Alerts.info('Información', 'Si no ha recibido el código de validación, por favor verifique su bandeja de entrada o la bandeja de correo no deseado. Si el código tarda mucho en llegar, presione el botón Mostrar Código el cual aparecera 90seg despues de la solicitud de inicio de sesion.', 15000);
    }, 15000);

    setTimeout(() => {
        this.getCode = true;
    }, 90000)
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
    },
    showCode() {
      this.viewCode = true;
    } 
  }
};
</script>
