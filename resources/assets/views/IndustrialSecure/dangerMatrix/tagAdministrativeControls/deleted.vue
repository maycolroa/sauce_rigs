<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="ELIMINAR TAG"
      url="industrialsecure-dangermatrix-tags-administrative-controls"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                :url="`/industrialSecurity/tags/administrativeControls/${this.$route.params.id}`"
                method="PUT"
                :tag="data"
                :is-edit="true"
                :is-deleted="true"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-administrative-controls'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormAdministrativeControlsComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-tag-administrative-controls-deleted',
  metaInfo: {
    title: 'Controles Administrativos - Eliminar'
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
    axios.get(`/industrialSecurity/tags/administrativeControls/${this.$route.params.id}`)
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