<template>
  <div class="authentication-wrapper authentication-2 px-4">
    <div class="authentication-inner py-5">

        <!-- Logo -->
        <div class="d-flex justify-content-center align-items-center">
            <img class="ui-w-200 rounded-circle" src="~@/icons/Sauce.png">
        </div>
        <!-- / Logo -->

        <!-- Form -->
        <!-- <form class="my-3" :action="loginAction" @submit.prevent="submit"> -->
        <form class="my-3" :action="action" @submit.prevent="submit">
            <h1 v-if="state != 'without use'">Este link ya no se puede volver a reutilizar por qué ya fue utilizado.</h1>
            <div v-else>
                <vue-input v-model="form.password"
                label="Nueva contraseña"
                name="password"
                type="password"
                :autocomplete="true"
                placeholder="Escribe tu nueva contraseña"
                :error="form.errorsFor('password')"
                ></vue-input>
                <vue-input v-model="form.password_confirmation"
                label=""
                name="password_confirmation"
                type="password"
                :autocomplete="true"
                placeholder="Vuelve a escribir tu nueva contraseña"
                :error="form.errorsFor('password_confirmation')"
                ></vue-input>
                <b-btn type="submit" variant="primary" :disabled="loading">Aceptar</b-btn>
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
import Form from '@/utils/Form.js';

export default {
    components: {
        VueInput
    },
    props: {
        state: {type: String, required: true},
        action: {type: String, required: true},
        method: {type: String, required: true},
        credentials: {
            default(){
                return {
                    password: '',
                    password_confirmation: ''
                }
            }
        }
    },
    data(){
        return {
            loading: false,
            form: Form.makeFrom(this.credentials, this.method),
        }
    },
    methods: {
        submit(e) {
            this.loading = true;
            this.form.submit(e.target.action)
            .then(response => {
                this.loading = false;
                location.href = "/";
            })
            .catch(error => {
                console.log(this.form);
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
