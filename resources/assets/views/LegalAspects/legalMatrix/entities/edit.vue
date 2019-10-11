<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="EDITAR ENTIDAD"
      url="legalaspects-lm-entity"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-entity-component
                :url="`/legalAspects/legalMatrix/entity/${this.$route.params.id}`"
                method="PUT"
                :entity="data"
                :is-edit="true"
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
  name: 'legalaspects-lm-entity-edit',
  metaInfo: {
    title: 'Entidades - Editar'
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