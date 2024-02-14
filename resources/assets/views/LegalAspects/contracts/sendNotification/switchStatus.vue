<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR PROGRAMACIÓN"
      url="contract-send-notification"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 400px">
            <form-program
              :url="`/legalAspects/notificationSend/program/${this.$route.params.id}`"
              :newsletter="data"
              :cancel-url="{ name: 'contract-send-notification'}"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormProgram from '@/components/LegalAspects/Contracts/SendNotifications/FormProgramComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'contract-send-notification-program',
  metaInfo() {
    return {
      title: 'Programar'
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
    axios.get(`/legalAspects/notificationSend/${this.$route.params.id}`)
      .then(response => {
          this.data = response.data.data;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
  }
}
</script>
