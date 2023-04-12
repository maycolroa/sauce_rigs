<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="EDITAR GRUPO DE COMPAÑIA"
      url="system-newslettersend"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-newsletter
                :url="`/system/newsletterSend/${this.$route.params.id}`"
                method="PUT"
                :newsletter="data"
                :is-edit="true"
                :cancel-url="{ name: 'system-newslettersend'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormNewsletter from '@/components/System/NewsletterSend/FormNewsletterComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-newslettersend-edit',
  metaInfo: {
    title: 'Boletin - Editar'
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
    axios.get(`/system/newsletterSend/${this.$route.params.id}`)
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