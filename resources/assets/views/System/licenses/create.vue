<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="CREAR LICENCIA"
      url="system-licenses"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-license
                url="/system/license"
                method="POST"
                :modules="modules"
                companies-data-url="/selects/companies"
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
  name: 'system-licenses-create',
  metaInfo: {
    title: 'Licencias - Crear'
  },
  components:{
    FormLicense
  },
  data(){
    return {
      modules: []
    }
  },
  created(){
    GlobalMethods.getLicenseModulesMultiselectGroup()
    .then(response => {
        this.modules = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
}
</script>
