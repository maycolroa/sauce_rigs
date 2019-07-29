<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Labels /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-label
                :url="`/system/label/${this.$route.params.id}`"
                method="PUT"
                :label="data"
                :is-edit="true"
                :cancel-url="{ name: 'system-labels'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLabel from '@/components/System/Labels/FormLabelComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-labels-edit',
  metaInfo: {
    title: 'Labels - Editar'
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
    axios.get(`/system/label/${this.$route.params.id}`)
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