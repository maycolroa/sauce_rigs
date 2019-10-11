<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="CREAR USUARIO"
      url="administrative-users"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
            <administrative-user-form 
                v-if="ready"
                url="/administration/users"
                method="POST"
                roles-data-url="/selects/roles"
                headquartersDataUrl="/selects/headquarters"
                systemApplyDataUrl="/selects/legalMatrix/systemApply"
                :filters-config="filtersConfig"
                :cancel-url="{ name: 'administrative-users'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeUserForm from '@/components/Administrative/Users/FormUserComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'administrative-users-create',
  metaInfo: {
    title: 'Usuarios - Crear'
  },
  components:{
    AdministrativeUserForm,
    Loading
  },
  data(){
    return {
      ready: false,
      filtersConfig: {}
    }
  },
  created(){
    axios.post(`/administration/users/filtersConfig`)
		.then(response => {
      this.filtersConfig = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
    });
  },
}
</script>
