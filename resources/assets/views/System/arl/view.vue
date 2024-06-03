<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="VER ARL"
      url="system-arl"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1500px">
            <form-arl
                :arl="data"
                :view-only="true"
                :cancel-url="{ name: 'system-arl'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormArl from '@/components/System/Arl/FormArlComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-arl-view',
  metaInfo: {
    title: 'ARL - Ver'
  },
  components:{
    FormArl
  },
  data () {
    return {
      data: [],
      modules: []
    }
  },
  created(){
    axios.get(`/system/employeeArl/${this.$route.params.id}`)
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