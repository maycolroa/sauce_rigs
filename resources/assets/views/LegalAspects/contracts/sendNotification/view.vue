<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="VER COMUNICACIÓN"
      url="contract-send-notification"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-newsletter
                :notification="data"
                :view-only="true"
                :cancel-url="{ name: 'contract-send-notification'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormNewsletter from '@/components/LegalAspects/Contracts/SendNotifications/FormNotificationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'system-newslettersend-view',
  metaInfo: {
    title: 'Envio de Comunicación - Ver'
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
    });
  },
  methods: {}
}
</script>