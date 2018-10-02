<template>
  <div class="authentication-wrapper authentication-2 px-4">
    <div class="authentication-inner py-5">

      <!-- Logo -->
      <div class="d-flex justify-content-center align-items-center">
        <img class="ui-w-200 rounded-circle" src="~@/icons/Sauce.png">
      </div>
      <!-- / Logo -->

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
          text-block="¿Olvidaste tu contraseña?"
          :action-block="passwordResetAction"
          ></vue-input>
        <div class="d-flex justify-content-between align-items-center m-0">
          <b-check name="rememberMe" v-model="form.rememberMe" class="m-0">Recuerdame</b-check>
          <b-btn type="submit" variant="primary" :disabled="loading">Ingresar</b-btn>
        </div>
      </form>
      <!-- / Form -->
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
    VueCheckbox,
  },
  props: {
    loginAction: {type: String, required: true},
    loginMethod: {type: String, required: true},
    passwordResetAction: {type:String, required: true},
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
  methods: {
      submit(e) {
        this.loading = true;
          this.form.submit(e.target.action, true)
          .then(response => {
              this.loading = false;
              location.href = "/";
          })
          .catch(error => {
                 this.loading = false;
              this.$notify({
                group: 'auth',
                title: 'Error',
                text: error
              });
          });
      }
  },
}
</script>
