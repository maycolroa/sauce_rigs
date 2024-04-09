<template>
  <div>
   <header-module
      title="CAPACITACIONES"
      subtitle="EDITAR CAPACITACIÓN"
      url="industrialsecure-roadSafety-trainings"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-training-component
                :url="`/industrialSecurity/roadsafety/training/${this.$route.params.id}`"
                method="PUT"
                :training="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-roadSafety-trainings'}"
                activitiesUrl="/selects/contracts/ctActivities"
                typeQuestionUrl="/selects/contracts/ctTrainingTypeQuestions"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTrainingComponent from '@/components/IndustrialSecure/RoadSafety/Trainings/FormTrainingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'roadSafety-trainings-edit',
  metaInfo: {
    title: 'Capacitaciones- Editar'
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
    axios.get(`/industrialSecurity/roadsafety/training/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        //this.$router.go(-1);
    });
  },
}
</script>