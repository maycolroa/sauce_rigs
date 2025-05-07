<template>
  <div class="authentication-wrapper authentication-2 px-4">
    <div class="authentication-inner py-5">

      <!-- Logo -->
      <div class="d-flex justify-content-center align-items-center" style="padding-bottom: 20px;">
        <img style="width: 400px; height: 100px;" src="~@/images/Sauce-ML Logo RiGS Principal.png">
      </div>
      <!-- / Logo -->
      <div style="width: 400px;">
      <p>Únete y conoce esta herramienta para gestionar los procesos de seguridad y salud en su empresa</p>
      </div>

      <hr class="border-dark mt-0 mb-4" style="padding-bottom: 30px;">

      <b-modal ref="modalInfo" :hideFooter="true" class="modal-top" size="lg">
        <div slot="modal-title">
          Información<br>
        </div>
        <div class="text-center">
          <i class="ion ion-md-person display-2 d-block text-info"></i>
        </div>
        <br><br>
        <p class="text-center"><b>Pensando en tu seguridad, hemos implementado doble autentificación para el ingreso a SAUCE.
           Este cambio lo podras ver reflejado a partir de la proxima semana 5/05/2025.</b></p>
      </b-modal>

      <!-- Form -->
      <form class="my-3" :action="loginAction" @submit.prevent="submit">
        <vue-input v-model="form.email"
          label="Correo Electronico"
          name="email"
          type="email"
          :autocomplete="true"
          placeholder="Correo Electronico"
          :error="form.errorsFor('email')"
          ></vue-input>
        <vue-input v-model="form.password"
          label="Contraseña"
          name="password"
          type="password"
          placeholder="Contraseña"
          :error="form.errorsFor('password')"
        ></vue-input>
        <div class="d-flex align-items-left m-0" style="margin-top: 10%">
          <br><br><a :href="passwordResetHelper" class="d-block" style="color: #5d605e;">Ayuda para reestablecer tu contraseña</a>
        </div>
        <div class="d-flex justify-content-between align-items-center m-0">
          <b-check name="rememberMe" v-model="form.rememberMe" class="m-0">Recuérdame<br><a :href="passwordResetAction" class="d-block" style="color: #5d605e;">¿Olvidaste tu contraseña?</a></b-check>
          <b-btn type="submit" variant="primary" :disabled="loading">Iniciar sesión</b-btn>
        </div>
      </form>
      <!-- / Form -->

      <!--<b-modal ref="inforUser" :hideFooter="true" class="modal-top" size="lg">
        <div slot="modal-title">
          <h3>Información</h3>
        </div>
        <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
          <h4>Estimado usuario, si ingresa por primera vez al sistema, por favor diríjase a la opción "¿Olvidaste tu contraseña?"</h4>
        </b-card>
        <br>
        <div class="row float-right pt-12 pr-12y">
          <b-btn variant="primary" @click="hideModal">Cerrar</b-btn>
        </div>
      </b-modal>-->

    </div>
  </div>
</template>

<!-- Page -->
<style src="@/vendor/styles/bootstrap.scss" lang="scss"></style>
<style src="@/vendor/styles/appwork.scss" lang="scss"></style>
<style src="@/vendor/styles/theme-cotton.scss" lang="scss"></style>
<style src="@/vendor/styles/colors.scss" lang="scss"></style>
<style src="@/vendor/styles/uikit.scss" lang="scss"></style>
<style src="@/vendor/libs/vue-notification/vue-notification.scss" lang="scss"></style>
<style src="@/app.scss" lang="scss"></style>
<style src="@/vendor/styles/pages/authentication.scss" lang="scss"></style>
<script>
import VueInput from '@/components/Inputs/VueInput.vue';
import VueCheckbox from '@/components/Inputs/VueCheckbox.vue';
import Form from '@/utils/Form.js';

export default {
  components: {
    VueInput,
    VueCheckbox
  },
  props: {
    loginAction: {type: String, required: true},
    loginMethod: {type: String, required: true},
    passwordResetAction: {type:String, required: true},
    passwordResetHelper: {type:String, required: true},
    credentials: {
      default(){
        return {
          email: '',
          password: '',
          rememberMe: false
        }
      }
    }
  },
  data(){
    return {
      loading: false,
      form: Form.makeFrom(this.credentials, this.loginMethod),
    }
  },
  mounted(){
    this.$refs.modalInfo.show()
    //this.$refs.inforUser.show()
  },
  methods: {
      submit(e) {
        this.loading = true;
          this.form.submit(e.target.action, true)
          .then(response => {
              this.loading = false;

              if (response.data.redirectTo != undefined)
              {
                //console.log(response.data.redirectTo)
                location.href = response.data.redirectTo;
              }
              else 
              {
                //console.log(response)
                location.href = "/";
              }
          })
          .catch(error => {
                 this.loading = false;
              this.$notify({
                group: 'auth',
                title: 'Error',
                text: error
              });
          });
      },
      hideModal() {
        this.$refs.modalInfo.hide()
      },
  },
}
</script>
