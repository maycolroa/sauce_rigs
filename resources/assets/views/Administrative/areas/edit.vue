<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="EDITAR ÁREA"
      url="administrative-areas"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-area-form 
                :url="`/administration/area/${this.$route.params.id}`"
                method="PUT"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                processes-data-url="/selects/processes"
                :disable-wacth-select-in-created="true"
                :area="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-areas'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeAreaForm from '@/components/Administrative/Areas/FormAreaComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-areas-edit',
  metaInfo: {
    title: 'Áreas - Editar'
  },
  components:{
    AdministrativeAreaForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/area/${this.$route.params.id}`)
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