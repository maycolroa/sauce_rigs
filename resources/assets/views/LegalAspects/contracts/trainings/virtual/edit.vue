<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="EDITAR CAPACITACIÓN VIRTUAL"
      url="legalaspects-contracts-trainings-virtual"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-training-component
                :url="`/legalAspects/trainingContract/${this.$route.params.id}`"
                method="PUT"
                :training="data"
                :is-edit="true"
                :cancel-url="{ name: 'legalaspects-contracts-trainings-virtual'}"
                activitiesUrl="/selects/contracts/ctActivities"
                typeQuestionUrl="/selects/contracts/ctTrainingTypeQuestions"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTrainingComponent from '@/components/LegalAspects/Contracts/Trainings/FormTrainingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contracts-trainings-virtual-edit',
  metaInfo: {
    title: 'Capacitaciones Virtuales- Editar'
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
        console.log(this.data)
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>