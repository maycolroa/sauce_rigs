<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="EDITAR ETIQUETA"
      url="administrative-customlabels"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-label
                :url="`/administration/label/${this.$route.params.id}`"
                method="PUT"
                :label="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-customlabels'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLabel from '@/components/Administrative/Labels/FormLabelComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-customlabels-edit',
  metaInfo: {
    title: 'Configurar Etiqueta - Editar'
  },
  components:{
    FormLabel
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/administration/label/${this.$route.params.id}`)
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