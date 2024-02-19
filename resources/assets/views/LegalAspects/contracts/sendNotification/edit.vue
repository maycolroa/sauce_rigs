<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="EDITAR COMUNICACIÓN"
      url="contract-send-notification"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-newsletter
                :url="`/legalAspects/notificationSend/${this.$route.params.id}`"
                method="PUT"
                :notification="data"
                :is-edit="true"
                :cancel-url="{ name: 'contract-send-notification'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormNewsletter from '@/components/LegalAspects/Contracts/SendNotifications/FormNotificationComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'contract-send-notification-edit',
  metaInfo: {
    title: 'Envio de Comunicación - Editar'
  },
  components:{
    FormNewsletter
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
        this.$router.go(-1);
    });
  }
}
</script>