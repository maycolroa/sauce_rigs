<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Módulo Favorito</span>
    </h4>
    <header-module
      title="MÓDULO FAVORITO"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
            <form-default-module
                v-if="ready"
                :url="`/administration/users/defaultModule`"
                method="POST"
                :modules="modules"
                :user="data"
                :cancel-url="{ path: '/'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormDefaultModule from '@/components/Administrative/Users/FormDefaultModule.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'default-module',
  metaInfo: {
    title: 'Módulo Favorito'
  },
  components:{
    FormDefaultModule,
    Loading
  },
  data () {
    return {
      data: [],
      modules: [],
      ready: false,
    }
  },
  created(){
    GlobalMethods.getModulesMultiselectGroup()
    .then(response => {
        this.modules = response;
        this.ready = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    axios.get(`/administration/users/myDefaultModule`)
		.then(response => {
      this.data = response.data.data;
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
  }
}
</script>