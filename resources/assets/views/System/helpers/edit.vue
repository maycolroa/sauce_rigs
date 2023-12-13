<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="EDITAR AYUDA"
      url="system-helpers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1500px">
            <form-helper
                :url="`/system/helpers/${this.$route.params.id}`"
                method="PUT"
                :help="data"
                modules-data-url="/selects/modules"
                :is-edit="true"
                :cancel-url="{ name: 'system-helpers'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormHelper from '@/components/System/Helpers/FormHelperComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-helpers-edit',
  metaInfo: {
    title: 'Ayuda - Editar'
  },
  components:{
    FormHelper
  },
  data () {
    return {
      data: [],
      modules: []
    }
  },
  created(){
    axios.get(`/system/helpers/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  mounted() {
    setTimeout(() => {
      GlobalMethods.getModulesMultiselectGroup()
      .then(response => {
          this.modules = response;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
    }, 2000)
  },
}
</script>