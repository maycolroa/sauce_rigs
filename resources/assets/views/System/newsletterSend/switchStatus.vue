<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="CREAR PROGRAMACIÓN"
      url="system-newslettersend"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 400px">
            <form-program
              :url="`/system/newsletterSend/program/${this.$route.params.id}`"
              :newsletter="data"
              :cancel-url="{ name: 'system-newslettersend'}"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormProgram from '@/components/System/NewsletterSend/FormProgramComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'newslettersend-program',
  metaInfo() {
    return {
      title: 'Cambiar Estado'
    }
  },
  components:{
    FormProgram
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/system/newsletterSend/${this.$route.params.id}`)
      .then(response => {
          this.data = response.data.data;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
  }
}
</script>
