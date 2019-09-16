<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Usuarios /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
            <administrative-user-form
                v-if="ready"
                :user="data"
                :view-only="true"
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
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-users-view',
  metaInfo: {
    title: 'Usuarios - Ver'
  },
  components:{
    AdministrativeUserForm,
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
      filtersConfig: {}
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