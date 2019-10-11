<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="VER LABEL"
      url="system-labels"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-label
                :label="data"
                :view-only="true"
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
  name: 'system-labels-view',
  metaInfo: {
    title: 'Labels - Ver'
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