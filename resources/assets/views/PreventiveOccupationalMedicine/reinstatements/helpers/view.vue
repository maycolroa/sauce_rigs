<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="VISUALIZAR AYUDAS"
      url="reinstatements-customHelpers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1000px">
            <form-helper
                :help="data"
                :view-only="true"
                :cancel-url="{ name: 'reinstatements-customHelpers'}"/>
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
  name: 'reinstatements-customHelpers-view',
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
    axios.get(`/biologicalmonitoring/reinstatements/helpers/${this.$route.params.id}`)
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