<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Usuarios /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-user-form
                :url="`/administration/users/${this.$route.params.id}`"
                method="PUT"
                :user="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-users'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeUserForm from '@/components/Administrative/Users/FormUserComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-users-edit',
  metaInfo: {
    title: 'Usuarios - Editar'
  },
  components:{
    AdministrativeUserForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/users/${this.$route.params.id}`)
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