<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Roles /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-role-form 
                :url="`/administration/role/${this.$route.params.id}`"
                method="PUT"
                :permissions="permissions"
                :role="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-roles'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeRoleForm from '@/components/Administrative/roles/FormRoleComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'administrative-roles-edit',
  metaInfo: {
    title: 'Roles - Editar'
  },
  components:{
    AdministrativeRoleForm
  },
  data () {
    return {
      data: [],
      permissions:[]
    }
  },
  created(){
    axios.get(`/administration/role/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
    
    GlobalMethods.getPermissionsMultiselect()
    .then(response => {
        this.permissions = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>