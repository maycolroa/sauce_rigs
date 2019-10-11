<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="EDITAR SEDES"
      url="administrative-headquarters"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-headquarter-form 
                :url="`/administration/headquarter/${this.$route.params.id}`"
                method="PUT"
                regionals-data-url="/selects/regionals"
                :headquarter="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-headquarters'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeHeadquarterForm from '@/components/Administrative/Headquarters/FormHeadquarterComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-headquarters-edit',
  metaInfo: {
    title: 'Sedes - Editar'
  },
  components:{
    AdministrativeHeadquarterForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/headquarter/${this.$route.params.id}`)
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