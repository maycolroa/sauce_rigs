<template>
  <div class="authentication-wrapper authentication-2 px-4">
    <div class="authentication-inner py-5">
      <!-- Form -->
      <form class="card" :action="action" @submit.prevent="submit">
        <div class="p-4 p-sm-5">
          <!-- Logo -->
            <div class="d-flex justify-content-center align-items-center">
                <img style="width: 300px; height: 80px;" src="~@/images/Sauce-ML Logo RiGS Principal.png">
            </div>
         <!-- / Logo -->
            
          <h5 class="text-center text-muted font-weight-normal mb-4 py-3">Restablece tu contraseña</h5>

          <hr class="mt-0 mb-4">
        <vue-input v-model="form.email"
          label="Correo Electronico"
          name="email"
          type="email"
          :autocomplete="true"
          placeholder="Ingresa tu correo Electronico"
          :error="form.errorsFor('email')"
          ></vue-input>
        <vue-input v-model="form.password"
          label="Contraseña"
          name="password"
          type="password"
          help-text="La contraseña debe contener mínimo 8 caracteres entre los cuales debe haber al menos un número y un caracter especial (@$!%?&*._-)."
          placeholder="Contraseña"
          :error="form.errorsFor('password')"
        ></vue-input>
        <vue-input v-model="form.password_confirmation"
          label="Confirmar Contraseña"
          name="password_confirmation"
          type="password"
          placeholder="Confirmar Contraseña"
          :error="form.errorsFor('password_confirmation')"
        ></vue-input>

          <b-btn type="submit" variant="primary" :disabled="loading" :block="true">Restablecer contraseña</b-btn>
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
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput
  },
  props: {
    action: { type: String, required: true },
    method: { type: String, required: true },
    token: { type: String, required: true },
    data: {
      default() {
        return {
          email: "",
          password: "",
          password_confirmation: "",
          token: this.token
        };
      }
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.data, this.loginMethod)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          location.href = "/";
        })
        .catch(error => {
          if (error.response.status == 422) {
            this.loading = false;
          }
          if (error.response.status == 500) {
            this.loading = false;
          }
        });
    }
  }
};
</script>
