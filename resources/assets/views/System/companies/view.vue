<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="VER COMPAÑIA"
      url="system-companies"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-company
                :company="data"
                :view-only="true"
                :cancel-url="{ name: 'system-companies'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormCompany from '@/components/System/Companies/FormCompanyComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-companies-view',
  metaInfo: {
    title: 'Compañias - Ver'
  },
  components:{
    FormCompany
  },
  data () {
    return {
      data: [],
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
  },
}
</script>