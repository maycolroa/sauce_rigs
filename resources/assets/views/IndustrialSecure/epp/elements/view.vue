<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="VER ELEMENTO"
      url="industrialsecure-epps-elements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <element-form
                :element="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-epps-elements'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import ElementForm from '@/components/IndustrialSecure/Epp/FormElementComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-epps-elements-view',
  metaInfo: {
    title: 'Elementos - Ver'
  },
  components:{
    ElementForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/epp/element/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>