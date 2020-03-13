<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="VER ACTIVIDAD"
      url="legalaspects-contracts-activities"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <contract-activity-form
                :activity="data"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-contracts-activities'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import ContractActivityForm from '@/components/LegalAspects/Contracts/Activities/FormContractActivityComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contracts-activities-view',
  metaInfo: {
    title: 'Actividades - Ver'
  },
  components:{
    ContractActivityForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/activityContract/${this.$route.params.id}`)
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