<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="EDITAR USUARIO"
      url="administrative-users"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <template v-if="form == 'default'">
              <form-user
                :url="`/administration/users/${this.$route.params.id}`"
                method="PUT"
                roles-data-url="/selects/roles"
                headquartersDataUrl="/selects/headquarters"
                systemApplyDataUrl="/selects/legalMatrix/systemApply"
                :user="data"
                :filters-config="filtersConfig"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-users'}"/>
            </template>
            <template v-if="form == 'hptu'">
              <form-user-hptu
                :url="`/administration/users/${this.$route.params.id}`"
                method="PUT"
                roles-data-url="/selects/roles"
                headquartersDataUrl="/selects/headquarters"
                systemApplyDataUrl="/selects/legalMatrix/systemApply"
                :user="data"
                :filters-config="filtersConfig"
                :is-edit="true"
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
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-users-edit',
  metaInfo: {
    title: 'Usuarios - Editar'
  },
  components:{
    FormUser,
    FormUserHptu,
    Loading
  },
  data () {
    return {
      form: '',
      data: [],
      ready: false,
      filtersConfig: {}
    }
  },
  created(){    
    axios.post(`/configurableForm/formModel`, {key: 'form_user'})
		.then(response => {
      this.form = response.data;

      axios.post(`/administration/users/filtersConfig`)
      .then(response2 => {
        this.filtersConfig = response2.data;

        axios.get(`/administration/users/${this.$route.params.id}`)
        .then(response3 => {
            this.data = response3.data.data;
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
    })
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
    });
  },
}
</script>