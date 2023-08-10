<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="EDITAR LICENCIA"
      url="system-licenses"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1500px">
            <form-license
                :url="`/system/license/${this.$route.params.id}`"
                method="PUT"
                :license="data"
                :modules="modules"
                companies-data-url="/selects/companies"
                :is-edit="true"
                :view-only="freeze"
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
  name: 'system-licenses-edit',
  metaInfo: {
    title: 'Licencias - Editar'
  },
  components:{
    FormLicense
  },
  data () {
    return {
      data: [],
      modules: [],
      freeze: false
    }
  },
  created(){
    axios.get(`/system/license/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;

        if (this.data.freeze == 'SI')
        {
          this.freeze = true;
        }
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