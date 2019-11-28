<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      :subtitle="`VER ${keywordCheck('process')}`"
      url="administrative-processes"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-process-form
                :process="data"
                :view-only="true"
                :disable-wacth-select-in-created="true"
                :cancel-url="{ name: 'administrative-processes'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import AdministrativeProcessForm from '@/components/Administrative/Processes/FormProcessComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-processes-edit',
  metaInfo() {
    return {
      title: `${this.keywordCheck('processes')} - Ver`
    }
  },
  components:{
    AdministrativeProcessForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/process/${this.$route.params.id}`)
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