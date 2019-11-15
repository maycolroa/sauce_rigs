<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="AGREGAR USUARIOS DE OTRA COMPAÑIA"
      url="administrative-users"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <add-user-other-company 
                url="/administration/users/addUserOtherCompany"
                method="POST"
                :cancel-url="{ name: 'administrative-users'}"
                roles-data-url="/selects/roles"
                :usersOptions="usersOptions"
                :rolesOptions="rolesOptions"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AddUserOtherCompany from '@/components/Administrative/Users/FormAddUserOtherCompany.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'add-users-other-company',
  metaInfo() {
    return {
      title: `Agregar usuarios de otra compañia`
    }
  },
  components:{
    AddUserOtherCompany
  },
  data () {
    return {
      rolesOptions: [],
      usersOptions: []
    }
  },
  created(){
    this.fetchSelect('usersOptions', '/selects/usersOtherCompany');
  },
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
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
