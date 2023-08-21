<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="ELIMINAR TAG"
      url="industrialsecure-dangermatrix-tags-engineering-controls"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                :url="`/industrialSecurity/tags/engineeringControls/${this.$route.params.id}`"
                method="PUT"
                :tag="data"
                :is-edit="true"
                :is-deleted="true"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-engineering-controls'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormEngineeringControlsComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-tag-engineering-controls-deleted',
  metaInfo: {
    title: 'Controles de Ingenieria - Eliminar'
  },
  components:{
    FormTag
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/tags/engineeringControls/${this.$route.params.id}`)
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