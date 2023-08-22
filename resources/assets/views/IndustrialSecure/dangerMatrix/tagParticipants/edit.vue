<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="EDITAR TAG"
      url="industrialsecure-dangermatrix-tags-participants"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                :url="`/industrialSecurity/tags/participants/${this.$route.params.id}`"
                method="PUT"
                :tag="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-participants'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormParticipantsComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-tag-participants-edit',
  metaInfo: {
    title: 'Participantes - Editar'
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
    axios.get(`/industrialSecurity/tags/participants/${this.$route.params.id}`)
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