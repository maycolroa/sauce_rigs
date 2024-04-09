<template>
  <div>
    <header-module
      title="CAPACITACIONES"
      subtitle="VER CAPACITACIÓN REALIZADA"
      url="industrialsecure-roadsafety-trainings-employees"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-training-component
                :training="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-roadsafety-trainings-employees'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormTrainingComponent from '@/components/IndustrialSecure/RoadSafety/Trainings/FormTrainingEmployeeViewComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'roadsafety-trainings-trainings-employees-view',
  metaInfo: {
    title: 'Capacitaciones Realizadas - Ver'
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
    axios.get(`/industrialSecurity/roadsafety/training/showEmployee/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });//
  },
}
</script>