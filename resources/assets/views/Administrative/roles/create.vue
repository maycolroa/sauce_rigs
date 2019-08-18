<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Roles /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-role-form 
                url="/administration/role"
                method="POST"
                :all-modules="allModules"
                :modules="modules"
                :permissions="permissions"
                :cancel-url="{ name: 'administrative-roles'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeRoleForm from '@/components/Administrative/Roles/FormRoleComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'administrative-roles-create',
  metaInfo: {
    title: 'Roles - Crear'
  },
  components:{
    AdministrativeRoleForm
  },
  data(){
    return {
      allModules: [],
      modules: [],
      permissions:[]
    }
  },
  created(){
    GlobalMethods.getPermissionsMultiselect()
    .then(response => {
        this.permissions = response;
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

    GlobalMethods.getModulesMultiselectGroup()
    .then(response => {
        this.allModules = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
}
</script>
