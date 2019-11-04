<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="EDITAR COMPAÑIA"
      url="system-companies"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-company
                :url="`/system/company/${this.$route.params.id}`"
                method="PUT"
                :company="data"
                :is-edit="true"
                :cancel-url="{ name: 'system-companies'}"
                :usersOptions="usersOptions"
                :rolesOptions="rolesOptions"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCompany from '@/components/System/Companies/FormCompanyComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-companies-edit',
  metaInfo: {
    title: 'Compañias - Editar'
  },
  components:{
    FormCompany
  },
  data () {
    return {
      data: [],
      rolesOptions: [],
      usersOptions: []
    }
  },
  created(){
    axios.get(`/system/company/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.fetchSelect('usersOptions', '/selects/system/usersCompany');
        this.fetchSelect('rolesOptions', '/selects/system/rolesCompany');
        
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
  methods: {
      fetchSelect(key, url)
      {
          GlobalMethods.getDataMultiselect(url, {id: this.$route.params.id})
          .then(response => {
              this[key] = response;
          })
          .catch(error => {
              Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
              this.$router.go(-1);
          });
      },
  }
}
</script>