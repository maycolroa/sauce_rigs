<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="EDITAR LICENCIA"
      url="system-licenses"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-license
                url="/system/license/reasignar"
                method="POST"
                :license="data"
                :modules="modules"
                companies-data-url="/selects/companiesGroupSpecific"
                :is-edit="true"
                :cancel-url="{ name: 'system-licenses-reasignacion-index'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLicense from '@/components/System/Licenses/FormLicenseReasignarComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-licenses-reasignar-edit',
  metaInfo: {
    title: 'Licencias - Editar'
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
    axios.get(`/system/license/getReasignar/${this.$route.params.id}`)
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