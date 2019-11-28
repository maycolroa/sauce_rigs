<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`EDITAR ${keywordCheck('regionals', 'Regionales')}`"
      url="administrative-regionals"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-regional-form
                :url="`/administration/regional/${this.$route.params.id}`"
                method="PUT"
                :regional="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-regionals'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeRegionalForm from '@/components/Administrative/Regionals/FormRegionalComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-regionals-edit',
  metaInfo() {
    return {
      title: `${this.keywordCheck('regionals', 'Regionales')} - Editar`
    }
  },
  components:{
    AdministrativeRegionalForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/regional/${this.$route.params.id}`)
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