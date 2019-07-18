<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Licencias /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-license
                :license="data"
                :modules="modules"
                :view-only="true"
                :cancel-url="{ name: 'system-licenses'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormLicense from '@/components/System/Licenses/FormLicenseComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-licenses-view',
  metaInfo: {
    title: 'Licencias - Ver'
  },
  components:{
    FormLicense
  },
  data () {
    return {
      data: [],
      modules: []
    }
  },
  created(){
    axios.get(`/system/license/${this.$route.params.id}`)
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
      GlobalMethods.getLicenseModulesMultiselectGroup()
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