<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`EDITAR ${keywordCheck('businesses')}`"
      url="administrative-businesses"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-business-form
                :url="`/administration/business/${this.$route.params.id}`"
                method="PUT"
                :business="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-businesses'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeBusinessForm from '@/components/Administrative/Businesses/FormBusinessComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-businesses-edit',
  metaInfo() {
    return {
      title: `${this.keywordCheck('businesses')} - Editar`
    }
  },
  components:{
    AdministrativeBusinessForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/business/${this.$route.params.id}`)
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