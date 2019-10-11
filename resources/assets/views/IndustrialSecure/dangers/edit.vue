<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGRO"
      subtitle="EDITAR PELIGRO"
      url="industrialsecure-dangers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-danger-form
                :url="`/industrialSecurity/danger/${this.$route.params.id}`"
                method="PUT"
                :danger="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-dangers'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureDangerForm from '@/components/IndustrialSecure/Dangers/FormDangerComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-dangers-edit',
  metaInfo: {
    title: 'Peligros - Editar'
  },
  components:{
    IndustrialSecureDangerForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/danger/${this.$route.params.id}`)
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