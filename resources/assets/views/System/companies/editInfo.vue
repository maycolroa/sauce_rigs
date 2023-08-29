<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="COMPLETAR INFORMACIÓN DE LA COMPAÑIA"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-company
                :url="`/system/company/${this.$route.params.id}`"
                method="PUT"
                :company="data"
                :is-edit="true"
                :cancel-url="{ name: ''}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCompany from '@/components/System/Companies/FormCompanyInfoComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-companies-edit-info',
  metaInfo: {
    title: 'Compañias - Información'
  },
  components:{
    FormCompany
  },
  data () {
    return {
      data: [],
      rolesOptions: [],
      usersOptions: []
    }
  },
  created(){
    axios.get(`/system/company/${this.$route.params.id}`)
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