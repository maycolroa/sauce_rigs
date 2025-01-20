<template>
  <div>
    <img style="width: 400px; height: 100px; margin-left: 38%; margin-top: 2%" src="~@/images/Sauce-ML Logo RiGS Principal.png">
    <hr class="border-dark mt-0 mb-4" style="margin-left: 30%; margin-top: 2%; margin-right: 30%">
    <b-row>
      <video style="width: 50%; height: 45%; margin-left: 25%;" controls>
        <source src="https://appsauce.s3.us-east-1.amazonaws.com/system/helpers/files/1/Restablecimiento+de+contrase%C3%B1a+(1).mp4" type="video/mp4">
      </video>
    </b-row>
    <b-row style="margin-left: 40%; margin-top: 2%; margin-bottom: 3%">
      <div>
          <b-btn variant="default" :to="cancelUrl">Iniciar sesión</b-btn>&nbsp;&nbsp;
          <b-btn variant="primary" :to="passwordResetAction">Ir a "¿Olvidaste tu contraseña?"</b-btn>&nbsp;&nbsp;
      </div>
    </b-row>
  </div>
</template>

<script>
export default {
  props: {
    cancelUrl: {type: String, required: true},
    passwordResetAction: {type:String, required: true},
  },
  watch: {}, 
  data() {
    return {
    };
  },
  mounted() {},
  methods: {}
};
</script>
