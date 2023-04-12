<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="CREAR BOLETIN"
      url="system-newslettersend"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-newsletter
                url="/system/newsletterSend"
                method="POST"
                :cancel-url="{ name: 'system-newslettersend'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormNewsletter from '@/components/System/NewsletterSend/FormNewsletterComponent.vue';

export default {
  name: 'system-newslettersend-create',
  metaInfo: {
    title: 'Envio de Boletin - Crear'
  },
  components:{
    FormNewsletter
  }
}
</script>
