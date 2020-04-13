<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="VER CAPACITACIÓN"
      url="legalaspects-contracts-trainings"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-training-component
                :training="data"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-contracts-trainings'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormTrainingComponent from '@/components/LegalAspects/Contracts/Trainings/FormTrainingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contracts-trainings-view',
  metaInfo: {
    title: 'Capacitaciones - Ver'
  },
  components:{
    FormTrainingComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/trainingContract/${this.$route.params.id}`)
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