<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR COMUNICACIÓN"
      url="contract-send-notification"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-newsletter
                url="/legalAspects/notificationSend"
                method="POST"
                :cancel-url="{ name: 'contract-send-notification'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormNewsletter from '@/components/LegalAspects/Contracts/SendNotifications/FormNotificationComponent.vue';

export default {
  name: 'contract-send-notification-create',
  metaInfo: {
    title: 'Envio de Comunicación - Crear'
  },
  components:{
    FormNewsletter
  }
}
</script>
