<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="EDITAR EPS"
      url="system-eps"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1500px">
            <form-eps
                :url="`/system/employeeEps/${this.$route.params.id}`"
                method="PUT"
                :eps="data"
                :is-edit="true"
                :cancel-url="{ name: 'system-eps'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEps from '@/components/System/Eps/FormEpsComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-eps-edit',
  metaInfo: {
    title: 'EPS - Editar'
  },
  components:{
    FormEps
  },
  data () {
    return {
      data: [],
      modules: []
    }
  },
  created(){
    axios.get(`/system/employeeEps/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
      console.log(error)
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
}
</script>