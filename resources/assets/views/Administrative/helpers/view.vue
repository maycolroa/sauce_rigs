<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="VER AYUDA"
      url="administrative-customHelpers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1000px">
            <form-helper
                :help="data"
                :view-only="true"
                :cancel-url="{ name: 'administrative-customHelpers'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormHelper from '@/components/Administrative/Helpers/FormHelperComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-customHelpers-view',
  metaInfo: {
    title: 'Ayuda - Ver'
  },
  components:{
    FormHelper
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/administration/helpers/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  mounted() {},
}
</script>