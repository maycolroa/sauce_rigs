<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CREAR NOTIFICACIÓN"
      url="contract-send-notification"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-newsletter
                url="/system/sendNotification"
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
    title: 'Envio de Boletin - Crear'
  },
  components:{
    FormNewsletter
  }
}
</script>
