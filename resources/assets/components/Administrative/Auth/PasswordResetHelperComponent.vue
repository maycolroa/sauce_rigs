<template>
  <div>
    <b-row>
      <video style="width: 70%; height: 600px; margin-left: 15%; margin-top: 5%" controls>
        <source src="https://appsauce.s3.us-east-1.amazonaws.com/system/helpers/files/1/Restablecimiento+de+contrase%C3%B1a+(1).mp4" type="video/mp4">
      </video>
    </b-row>
    <b-row style="margin-left: 80%; margin-top: 2%">
      <div>
          <b-btn variant="primary" :to="cancelUrl">Atras</b-btn>&nbsp;&nbsp;
      </div>
    </b-row>
  </div>
</template>

<script>
export default {
  props: {
    cancelUrl: {type: String, required: true},
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
