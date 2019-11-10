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
          <div v-if="ready">
            <template v-if="form == 'default'">
              <form-user
                url="/administration/users"
                method="POST"
                roles-data-url="/selects/roles"
                headquartersDataUrl="/selects/headquarters"
                systemApplyDataUrl="/selects/legalMatrix/systemApply"
                :filters-config="filtersConfig"
                :cancel-url="{ name: 'administrative-users'}"/>
            </template>
            <template v-if="form == 'hptu'">
              <form-user-hptu
                url="/administration/users"
                method="POST"
                roles-data-url="/selects/roles"
                headquartersDataUrl="/selects/headquarters"
                systemApplyDataUrl="/selects/legalMatrix/systemApply"
                :filters-config="filtersConfig"
                :cancel-url="{ name: 'administrative-users'}"/>
            </template>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormUser from '@/components/Administrative/Users/FormUserComponent.vue';
import FormUserHptu from '@/components/Administrative/Users/FormUserHptuComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'administrative-users-create',
  metaInfo: {
    title: 'Usuarios - Crear'
  },
  components:{
    FormUser,
    FormUserHptu,
    Loading
  },
  data(){
    return {
      form: '',
      ready: false,
      filtersConfig: {}
    }
  },
  created(){
    axios.post(`/configurableForm/formModel`, {key: 'form_user'})
		.then(response => {
      this.form = response.data;
      
      axios.post(`/administration/users/filtersConfig`)
      .then(response => {
        this.filtersConfig = response.data;
        this.ready = true
      })
      .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
      });
    })
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
    });
  },
}
</script>
