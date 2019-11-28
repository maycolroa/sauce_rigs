<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`EDITAR ${keywordCheck('position')}`"
      url="administrative-positions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-position-form
                :url="`/administration/position/${this.$route.params.id}`"
                method="PUT"
                :position="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-positions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativePositionForm from '@/components/Administrative/Positions/FormPositionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-positions-edit',
  metaInfo() {
    return {
      title: `${this.keywordCheck('positions')} - Editar`
    }
  },
  components:{
    AdministrativePositionForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/position/${this.$route.params.id}`)
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