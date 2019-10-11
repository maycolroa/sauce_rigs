<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="VER ENTIDAD"
      url="legalaspects-lm-entity"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-entity-component
                :entity="data"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-lm-entity'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormEntityComponent from '@/components/LegalAspects/LegalMatrix/Entity/FormEntityComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-interest-view',
  metaInfo: {
    title: 'Entidades - Ver'
  },
  components:{
    FormEntityComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/legalMatrix/entity/${this.$route.params.id}`)
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