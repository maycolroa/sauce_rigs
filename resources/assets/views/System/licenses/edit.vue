<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Licencias /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-license
                :url="`/system/license/${this.$route.params.id}`"
                method="PUT"
                :license="data"
                :modules="modules"
                companies-data-url="/selects/companies"
                :is-edit="true"
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

    GlobalMethods.getModulesMultiselectGroup()
    .then(response => {
        this.modules = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>