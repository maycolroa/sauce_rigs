<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="VER ROL"
      url="administrative-roles"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-role-form 
                :role="data"
                :view-only="true"
                :cancel-url="{ name: 'administrative-roles'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeRoleForm from '@/components/Administrative/Roles/FormRoleComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-roles-view',
  metaInfo: {
    title: 'Roles - Ver'
  },
  components:{
    AdministrativeRoleForm
  },
  data () {
    return {
      data: []
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
  },
}
</script>