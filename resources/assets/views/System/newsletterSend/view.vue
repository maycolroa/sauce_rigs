<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="VER GRUPO DE COMPAÑIA"
      url="system-newslettersend"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-newsletter
                :newsletter="data"
                :view-only="true"
                :cancel-url="{ name: 'system-newslettersend'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormNewsletter from '@/components/System/NewsletterSend/FormNewsletterComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'system-newslettersend-view',
  metaInfo: {
    title: 'Boletin - Ver'
  },
  components:{
    FormNewsletter
  },
  data () {
    return {
      data: [],
      siNo: []
    }
  },
  created(){
    this.fetchSelect('siNo', '/radios/siNo')
    axios.get(`/system/newsletterSend/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  methods: {
      fetchSelect(key, url)
      {
          GlobalMethods.getDataMultiselect(url, {id: this.$route.params.id})
          .then(response => {
              this[key] = response;
          })
          .catch(error => {
              Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
              this.$router.go(-1);
          });
      },
  }
}
</script>