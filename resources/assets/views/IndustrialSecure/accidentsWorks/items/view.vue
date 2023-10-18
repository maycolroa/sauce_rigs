<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="VER ITEM"
      url="industrialsecure-accidentswork-causes-items"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
        <loading :display="!ready"/>
          <div v-if="ready">
            <accident-form
                :item="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-accidentswork-causes-items'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
    
  </div>
</template>
 
<script>
import AccidentForm from '@/components/IndustrialSecure/AccidentsWork/Causes/FormItemComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'accidentsWork-causes-items-view',
  metaInfo: {
    title: 'Causas Items - Ver'
  },
  components:{
    AccidentForm,
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
    }
  },
  created(){
    axios.get(`/industrialSecurity/causes/items/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.ready = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  }
}
</script>